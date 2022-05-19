<script>

	import { theme } from '../store.js';

	function toggleTheme() {
		$theme = $theme === 'theme-light' ? 'theme-dark' : 'theme-light';
	}

</script>

<div class="icon-switch-mode icon icon-rounded theme-hover-light-on-secondary clickable" on:click={toggleTheme}></div>
