<script>

	import Header from './Header.svelte';
	import Footer from './Footer.svelte';
	import Calendar from './Calendar.svelte';
	import CalendarWeekly from './CalendarWeekly.svelte';
	import CalendarSwitch from './CalendarSwitch.svelte';

	import { screen, dates, calendarview } from '../store.js';

	function next() {
		$screen = 'Screen3';
	}

</script>

<Header>
	<span slot="title_left">
		<CalendarSwitch />
	</span>
	<span slot="title_center" class="text-center-row">
		<h2>Add your times</h2><h3>step 2/3</h3>
	</span>
</Header>

{#if $calendarview == 'monthly'}
<div class="container paper center-flex-row">
		<Calendar {next} dates={$dates} />
</div>
{:else}
<div class="container paper">
		<CalendarWeekly {next} dates={$dates} />
</div>
{/if}

<Footer />

<style>
  .container {
    width: 100vmin;
    height: 80vmin;
    border-radius: 1vmin;
    font-size: var(--font-size-regular);
		overflow: hidden;
  }
</style>