import tailwindcss from "@tailwindcss/vite";
import react from "@vitejs/plugin-react-swc";
import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";

export default defineConfig({
    css: {
        preprocessorOptions: {
            scss: {
                api: "modern",
            },
        },
    },
    plugins: [
        //Inspect(),
        laravel({
            input: "resources/js/app.tsx",
            refresh: true,
        }),
        react(),
        tailwindcss(),
    ],
    build: {
        outDir: "dist",
        lib: {
            entry: "resources/js/app.tsx",
            name: "LaraPrime",
            fileName: (format) => `laraprime.${format}.js`,
        },
        rollupOptions: {
            external: [
                "react",
                "react-dom",
                "@inertiajs/inertia",
                "@inertiajs/inertia-react",
                "primereact",
            ],
            output: {
                globals: {
                    react: "React",
                    "react-dom": "ReactDOM",
                    "@inertiajs/inertia": "Inertia",
                    "@inertiajs/inertia-react": "InertiaReact",
                },
            },
        },
    },
    resolve: {
        alias: {
            "@": "/resources/js",
        },
    },
});
