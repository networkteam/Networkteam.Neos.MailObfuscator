import { defineConfig } from "vite";

export default defineConfig({
  build: {
    outDir: "Resources/Public/Scripts",
    lib: {
      name: "nwt.mailobfuscation",
      entry: "Resources/Public/Scripts/nwt.mailobfuscation.js",
      formats: ["iife"],
      fileName: (format, entryName) => `${entryName}.min.js`,
    },
    emptyOutDir: false,
    copyPublicDir: false,
  },
});
