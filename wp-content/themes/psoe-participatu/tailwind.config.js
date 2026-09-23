/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./*.php",
    "./template-parts/**/*.php",
    "./inc/**/*.php",
    "./js/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          DEFAULT: "#E30613",
          dark: "#B0050F",
          darker: "#7A030B",
          light: "#FF4D5A",
          soft: "#FDECEC",
          muted: "#F9F1F0",
        },
        ink: {
          DEFAULT: "#1C1214",
          soft: "#5B4B4E",
          muted: "#8A7A7D",
        },
        cream: "#FAF7F5",
        wine: "#241114",
        wine2: "#1B0C0E",
      },
      fontFamily: {
        sans: ["Inter", "system-ui", "-apple-system", "Segoe UI", "Roboto", "Arial", "sans-serif"],
      },
      boxShadow: {
        card: "0 1px 2px rgba(0,0,0,.04), 0 8px 24px -12px rgba(0,0,0,.12)",
        hero: "0 24px 60px -20px rgba(227,6,19,.45)",
      },
      borderRadius: {
        xl2: "14px",
      },
      maxWidth: {
        shell: "1120px",
      }
    },
  },
  plugins: [],
};
