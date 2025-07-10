import { defineConfig } from 'vite'

export default defineConfig({
    build: {
        lib: {
            name: 'nwt.mailobfuscation',
            entry: 'Resources/Public/Scripts/nwt.mailobfuscation.js',
            formats: ['umd'],
            fileName: (format, entryName) => `${entryName}.min.js`
        },
        outDir: 'Resources/Public/Scripts',
        emptyOutDir: false,
        copyPublicDir: false,
    }
})