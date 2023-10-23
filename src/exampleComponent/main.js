import { createApp } from 'vue'
import axios from 'axios'
import App from './App.vue'

const http = axios.create({
	headers : {
		'X-WP-NONCE' : window.example.nonce
	}
})

const app = createApp(App)
app.config.globalProperties.$example = window.example
app.config.globalProperties.$http    = http
app.mount('#example')