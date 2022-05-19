<script>
	export let id = '';
	export let placeholder = '';
	export let value = '';
	export let oninput = false;
</script>

<input id="{id}" class="floating__input" name="{id}" type="text" placeholder="{placeholder}" bind:value={value} on:input={oninput} />
<label for="{id}" class="floating__label" data-content="{placeholder}">
	<span class="hidden--visually">{placeholder}</span>
</label>
