import {defineConfig, loadEnv} from 'vite';
import vue from '@vitejs/plugin-vue';
import autoprefixer from 'autoprefixer';
import tailwind from 'tailwindcss';
import {fileURLToPath, URL} from 'node:url';

// https://vite.dev/config/
export default ({mode}: { mode: string }) => {
	process.env = {...process.env, ...loadEnv(mode, process.cwd())};

	return defineConfig({
		base: process.env.VITE_BASE_URL,
		css: {
			postcss: {
				plugins: [tailwind(), autoprefixer()]
			}
		},
		plugins: [vue()],
		resolve: {
			alias: {
				'@': fileURLToPath(new URL('./src', import.meta.url))
			}
		},
		server: {
			host: true,
			port: 5050
		},
		preview: {
			host: true,
			port: 5050
		}
	});
}
