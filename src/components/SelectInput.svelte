<script>
	export let id = '';
	export let placeholder='';
	export let value;
	export let options = []; // EX: { [id,value],...[id,value] } OR [ '', ... '' ]
	export let onchange = false;
</script>

<select id={id}  class="floating__input" bind:value={value} on:change={onchange}>
{#each options as option}
	<option value={typeof option === 'string' ? option : option.id}>
		{typeof option === 'string' ? option : option.text}
	</option>
{/each}
</select>
<label for="{id}" class="floating__label" data-content="{placeholder}">
  <span class="hidden--visually">{placeholder}</span>
</label>