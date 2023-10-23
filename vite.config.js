import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import liveReload from 'vite-plugin-live-reload'
import * as dotenv from 'dotenv'
import fs from 'fs'
import path from 'path'

const getPages = () => {
	return {
		exampleComponent : './src/exampleComponent/main.js'
	}
}

const getPlugins = () => {
	const plugins = [
		vue()
	]

	const reload = [
		`${process.cwd()}/.env`
	]
	if (process.env.VITE_PHP_LIVE_RELOAD) {
		if (process.env.VITE_WP_CONFIG_LOCATION) {
			reload.push(`${process.cwd()}/app/**/*.php`)
			reload.push(process.env.VITE_WP_CONFIG_LOCATION)
		}
	}

	plugins.push(liveReload(reload, { root: '/' }))

	return plugins
}

const getHttps = () => {
	let https = false
	try {
		// Generate a certificate using: `mkcert domain.local` in the root dir.
		if (fs.existsSync('./' + process.env.VITE_DEV_SERVER_DOMAIN + '-key.pem')) {
			https = {
				key  : fs.readFileSync('./' + process.env.VITE_DEV_SERVER_DOMAIN + '-key.pem'),
				cert : fs.readFileSync('./' + process.env.VITE_DEV_SERVER_DOMAIN + '.pem'),
				ca   : fs.readFileSync(process.env.VITE_CRT_ROOT_CA)
			}
		}
	} catch (error) {
		console.log(error)
	}

	return https
}

export default () => {
	dotenv.config({ path: './.env', override: true })

	return defineConfig({
		plugins : getPlugins(),
		build   : {
			manifest      : true,
			rollupOptions : {
				input  : getPages(),
				output : {
					dir            : 'dist/assets/',
					assetFileNames : assetInfo => {
						const images = [
							'.png',
							'.jpg',
							'.jpeg',
							'.gif'
						]

						if (images.includes(path.extname(assetInfo.name))) {
							return 'images/[name].[hash][extname]'
						}

						return '[ext]/[name].[hash][extname]'
					},
					entryFileNames : 'js/[name].[hash].js',
					chunkFileNames : 'js/[name].[hash].js'
				}
			}
		},
		optimizeDeps : {
			force   : true,
			include : [
				'lodash'
			]
		},
		server : {
			https      : getHttps(),
			cors       : true,
			strictPort : true,
			host       : process.env.VITE_DEV_SERVER_DOMAIN || 'localhost',
			port       : process.env.VITE_DEV_SERVER_PORT || 8088,
			hmr        : {
				host : process.env.VITE_DEV_SERVER_DOMAIN || 'localhost',
				port : process.env.VITE_DEV_SERVER_PORT || 8088
			},
			watch : {
				disableGlobbing : false,
				usePolling      : true,
				interval        : 100
			}
		},
		css : {
			preprocessorOptions : {
				scss : {
					additionalData : [
						'@import "src/scss/variables.scss";',
						''
					].join('\n')
				}
			}
		}
	})
}