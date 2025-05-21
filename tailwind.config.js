import plugin from "tailwindcss/plugin";

/** @type {import('tailwindcss').Config} */
export default {
    content: ["./templates/**/*.{twig, js, ts}"],
    safelist:[
        "!bg-primary/20",
        "!hidden"
    ],
    theme: {
        colors: {
            transparent: "transparent",
            white: "#ffffff",
            primary: "#1e3a8a",
            secondary: "#535bf2",

            light: "#f3f4f6",
            dark: "#1e293b",

            gray: {
                200: "#e5e7eb",
                400: "#9ca3af",
                700: "#374151",
            },

            slate: {
                100: "#f1f5f9",
                200: "#e2e8f0",
                300: "#cbd5e1",
                500: "#64748b",
            },

            green: {
                500: "#22c55e",
            },

            red: {
                500: "#ef4444",
                700: "#c10007",
            },

            yellow: {
                500: "#eab308",
            },

            blue: {
                500: "#3b82f6",
            },

            orange: {
                500: "#ff6900",
            },
        },
    },
    
    plugins: [
        plugin(({ addComponents }) => {
            addComponents({
                ".btn": {
                    backgroundColor: "#1e3a8a",
                    borderRadius: "0.375rem",
                    display: "flex",
                    justifyContent: "center",
                    alignItems: "center",
                    color: "white",
                    height: "36px",
                    transition: ".2s all",
                    padding: "0 1rem",
                    "&:active": {
                        transform: "scale(0.95)",
                    },
                    "&:hover": {
                        opacity: "90%",
                    },
                },

                ".content-container": {
                    padding: "1rem",
                    backgroundColor: "#ffffff",
                    borderRadius: ".5rem",
                    position: "relative",
                    marginTop: "1rem",
                    boxShadow: "0 4px 6px 0 rgba(0, 0, 0, 0.1)",
                },

                ".dashboard-content-container": {
                    backgroundColor: "#ffffff",
                    borderRadius: "0.5rem",
                    overflow: "hidden",
                    position: "relative",
                    marginTop: "1rem",
                    height: "26.1rem",
                    boxShadow: "0 4px 6px 0 rgba(0, 0, 0, 0.1)",
                },

                ".w-container": {
                    borderRadius: "0.5rem",
                    backgroundColor: "#ffffff",
                    width: "100%",
                    padding: "1rem",
                    boxShadow: "0 4px 6px 0 rgba(0, 0, 0, 0.1)",
                },

                ".anim": {
                    transition: ".22s all",
                },
            });
        }),
    ],
}