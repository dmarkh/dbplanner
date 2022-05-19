<script>

	import { onMount } from 'svelte';
	import { screen } from '../store.js';
	import Header from './Header.svelte';
	import Footer from './Footer.svelte';
	import { gq_get_stats } from '../graphql-client.js';

	let npolls = false, nvotes = false;

	function next() {
		$screen = 'Screen1';
	}

	async function load_stats() {
		let res = await gq_get_stats();
		if ( res && res.data && res.data.getStats ) {
			npolls = res.data.getStats.npolls;
			nvotes = res.data.getStats.nvotes;
		}
	}

  onMount(async () => {
    setTimeout( () => load_stats(), 5000 );
  });

</script>

<Header>
	<span slot="title_center" class="text-center-row">
		<h2>Let's Schedule It!</h2><h3>polls for everyone</h3>
	</span>
</Header>

<div class="container paper-dimmed">
	<div class="container-top"></div>
	<div class="container-middle center-flex-col">

		<div class="text-large text-bold">DB PLANNER</div>
		<div class="text-regular margin-top">An easy way to schedule a time slot for an event.</div>

		<p>
			<button on:click={next}>SCHEDULE NEW MEETING POLL</button>
		</p>

	</div>
	<div class="container-bottom center-flex-col">
		<div class="calendar-icon"></div>
	</div>
</div>

<Footer>
<span slot="app-subfooter-center">
{#if npolls !== false && nvotes !== false }
	<div class="text-small">As of today, there were {npolls} polls created, {nvotes} votes cast</div>
{:else}
	<div class="text-small">© DMARKH 2022</div>
{/if}
</span>
</Footer>

<style>

	.container {
		width: 100vmin;
		height: 80vmin;
	  border-radius: 1vmin;
	  font-size: var(--font-size-regular);
  	box-sizing: border-box;
	  padding: 2vmin;
		display: flex;
		flex-direction: column;
	}

	.container-top {
		width: 100%;
		height: 25vmin;
		background-color: var(--color-tertiary);
	}

	.container-middle {
		width: 100%;
		height: 30vmin;
	}

	.container-bottom {
		width: 100%;
		height: 25vmin;
		background-color: var(--color-tertiary);
	}

	.calendar-icon {
		width:   25vmin;
		height:  25vmin;
		border:  0;
		margin:  0;
		padding: 0;
		background-image: url("../img/calendar.gif");
		background-size:cover;
	}

	.margin-top {
		margin-top: 3vmin;
	}

</style>