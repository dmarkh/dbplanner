<script>

	import ThemeSwitch from './ThemeSwitch.svelte';

</script>

<div id="app-header">
	<div id="app-subheader">
		<div id="app-subheader-left">
			<slot name="title_left"></slot>
		</div>
		<div id="app-subheader-center">
			<slot name="title_center"></slot>
		</div>
		<div id="app-subheader-right">
			<slot name="title_right">
				<ThemeSwitch />
			</slot>
		</div>
	</div>
</div>

<style>

	#app-header {
		width: 100vw;
		height: 10vmin;
		display: flex;
		justify-content: center;
		align-items: center;
		background-color: var(--color-primary);
		color: var(--font-color-light);
		font-size: var(--font-size-regular);
	}

	#app-subheader {
		width: 100vmin;
		height: 10vmin;
		display: flex;
		flex-direction: row;
	}

	#app-subheader-left, #app-subheader-right {
		width: 10vmin;
		height: 10vmin;
		display: flex;
		flex-direction: row;
		justify-content: center;
		align-items: center;
	}

	#app-subheader-center {
		width: 80vmin;
		height: 10vmin;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
	}

</style>