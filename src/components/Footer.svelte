<script>

	export let title_left = false;
	export let title_center = '<div class="text-small">© DMARKH 2022</div>';
	export let title_right = false;

</script>

<div id="app-footer">
	<div id="app-subfooter">
		<div id="app-subfooter-left">
		<slot name="app-subfooter-left">
			{#if title_left }
				{@html title_left }
			{/if}
		</slot>
		</div>
		<div id="app-subfooter-center">
		<slot name="app-subfooter-center">
			{#if title_center }
				{@html title_center}
			{/if}
		</slot>
		</div>
		<div id="app-subfooter-right">
		<slot name="app-subfooter-right">
			{#if title_right }
				{@html title_right}
			{/if}
		</slot>
		</div>
	</div>
</div>

<style>

	#app-footer {
		width: 100vw;
		height: 10vmin;
		display: flex;
		justify-content: center;
		align-items: center;
		background-color: var(--color-primary);
		color: var(--font-color-light);
		font-size: var(--font-size-regular);
	}

	#app-subfooter {
		width: 100vmin;
		height: 10vmin;
		display: flex;
		flex-direction: row;
	}

	#app-subfooter-left, #app-subfooter-right {
		width: 10vmin;
		height: 10vmin;
		display: flex;
		flex-direction: row;
		justify-content: center;
		align-items: center;
	}

	#app-subfooter-center {
		width: 80vmin;
		height: 10vmin;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
	}

</style>