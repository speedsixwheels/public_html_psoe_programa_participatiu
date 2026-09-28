/** Save each conversation when its session becomes idle, as a Markdown file in GitHub. */
export default {
  id: "conversations-to-github",
  async setup(ctx) {
    const repository = process.env.GITHUB_REPOSITORY ?? ""
    const token = process.env.GITHUB_TOKEN
    const branch = process.env.GITHUB_BRANCH || "main"
    const directory = process.env.GITHUB_CONVERSATIONS_DIRECTORY || "opencode-conversations"

    if (!/^[^/]+\/[^/]+$/.test(repository)) {
      console.warn("[conversations-to-github] Define GITHUB_REPOSITORY as owner/repo.")
      return
    }
    if (!token) {
      console.warn("[conversations-to-github] Define GITHUB_TOKEN in the environment to enable uploads.")
      return
    }

    const controller = new AbortController()
    const saving = new Set<string>()

    async function save(sessionID: string) {
      if (saving.has(sessionID)) return
      saving.add(sessionID)
      try {
        const [session, messages] = await Promise.all([
          ctx.session.get({ sessionID }),
          ctx.session.context({ sessionID }),
        ])
        const title = session.title || sessionID
        const transcript = messages.map((message) => {
          const value = message as unknown as Record<string, unknown>
          const role = String(value.role ?? value.type ?? "message")
          return `## ${role}\n\n\`\`\`json\n${JSON.stringify(message, null, 2)}\n\`\`\``
        }).join("\n\n")
        const content = `# ${title}\n\n- Session ID: \`${sessionID}\`\n- Project: \`${ctx.location.project.directory}\`\n\n${transcript}\n`
        const path = `${directory}/${sessionID}.md`.split("/").map(encodeURIComponent).join("/")
        const url = `https://api.github.com/repos/${repository}/contents/${path}`
        const headers = {
          Accept: "application/vnd.github+json",
          Authorization: `Bearer ${token}`,
          "X-GitHub-Api-Version": "2022-11-28",
          "Content-Type": "application/json",
        }

        const current = await fetch(`${url}?ref=${encodeURIComponent(branch)}`, {
          headers,
          signal: controller.signal,
        })
        let sha: string | undefined
        if (current.ok) sha = (await current.json() as { sha: string }).sha
        else if (current.status !== 404) throw new Error(`GitHub read failed (${current.status}): ${await current.text()}`)

        const response = await fetch(url, {
          method: "PUT",
          headers,
          signal: controller.signal,
          body: JSON.stringify({
            message: `Save OpenCode conversation: ${title}`,
            content: btoa(unescape(encodeURIComponent(content))),
            branch,
            ...(sha ? { sha } : {}),
          }),
        })
        if (!response.ok) throw new Error(`GitHub upload failed (${response.status}): ${await response.text()}`)
        console.info(`[conversations-to-github] Saved conversation to ${repository}/${path}`)
      } catch (error) {
        console.error(`[conversations-to-github] Failed to save ${sessionID}:`, error)
      } finally {
        saving.delete(sessionID)
      }
    }

    void (async () => {
      try {
        for await (const event of ctx.event.subscribe({ signal: controller.signal })) {
          if (event.type !== "session.idle") continue
          const payload = "data" in event ? event.data : undefined
          if (payload && "sessionID" in payload && typeof payload.sessionID === "string") {
            await save(payload.sessionID)
          }
        }
      } catch (error) {
        if (!controller.signal.aborted) console.error("[conversations-to-github] Event listener failed:", error)
      }
    })()

    return () => controller.abort()
  },
}
