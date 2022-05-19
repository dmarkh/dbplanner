<script>
	import { calendarview } from '../store.js';

	function toggle_view() {
		$calendarview = $calendarview == 'monthly' ? 'weekly' : 'monthly';
	}

</script>
{#if $calendarview == 'monthly'}
	<div class="icon-calendar-view-week icon icon-rounded theme-hover-light-on-secondary clickable text-large" on:click={toggle_view}></div>
{:else}
	<div class="icon-calendar-view-month icon icon-rounded theme-hover-light-on-secondary clickable text-large" on:click={toggle_view}></div>
{/if}