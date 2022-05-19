<script>

	import Header from './Header.svelte';
	import Footer from './Footer.svelte';

	import TextInput from './TextInput.svelte';
	import SelectInput from './SelectInput.svelte';

	import poll_timezone_options from '../timezones.js';
	import { capitalize_first_letter, uuidv4 } from '../util.js';

	import {
		screen,
		poll_id,
		poll_title,
		poll_cid,
		poll_cname,
		poll_notes,
		poll_location,
		poll_videolink,
		poll_timezone,
		user_id,
		user_name
	} from '../store.js';

	$poll_timezone = $poll_timezone ?? ( moment.tz.guess(true) ?? 'America/New_York' );

	$poll_cid = $user_id;
	$poll_cname = $user_name;

	function input_required( element_id ) {
  	let el = document.getElementById( element_id );
	  if ( el ) {
  	  el.classList.add('input-required');
	    setTimeout(() => {
      	el.classList.remove('input-required');
    	}, 1000 );
  	}
	}

	function next() {
		if ( $poll_title.length && $user_name.length ) {
			$poll_title = capitalize_first_letter($poll_title);
			$user_name = capitalize_first_letter($user_name);
			$poll_cname = $user_name;
			$screen = 'Screen2';
		} else {
			if ( !$poll_title.length ) { input_required('poll_title'); }
			if ( !$user_name.length ) { input_required('user_name'); }
		}
	}

</script>

<Header>
	<span slot="title_center" class="text-center-row">
		<h2>Provide Details</h2><h3>step 1/3</h3>
	</span>
</Header>

<div class="container paper center-flex-col">

	<h1><p>Meeting Poll Setup:</p></h1>

<div class="table-container">
<table class="tbl">
<tr>
	<td align="center">
		<div class="icon-title text-regular"></div>
	</td>
	<td>
		<TextInput id="poll_title" placeholder="* Meeting Title" bind:value={$poll_title} />
	</td>
</tr>
<tr>
	<td align="center">
		<div class="icon-creator text-regular"></div>
	</td>
	<td>
		<TextInput id="user_name" placeholder="* Your Name" bind:value={$user_name} />
	</td>
</tr>
<tr>
	<td align="center">
		<div class="icon-notes text-regular"></div>
	</td>
	<td align="left">
		<TextInput id="poll_notes" placeholder="Notes (optional)" bind:value={$poll_notes} />
	</td>
</tr>
<tr>
	<td align="center">
		<div class="icon-location text-regular"></div>
	</td>
	<td align="left">
		<TextInput id="poll_location" placeholder="Location (optional)" bind:value={$poll_location} />
	</td>
</tr>
<tr>
	<td align="center">
		<div class="icon-videolink text-regular"></div>
	</td>
	<td align="left">
		<TextInput id="poll_videolink" placeholder="VideoConference Link (optional)" bind:value={$poll_videolink} />
	</td>
</tr>
<tr>
	<td align="center">
		<div class="icon-timezone text-regular"></div>
	</td>
	<td align="left">
		<SelectInput id="poll_timezone" placeholder="Meeting Timezone" bind:value={$poll_timezone} options={poll_timezone_options} />
	</td>
</tr>
</table>
</div>

<p class="center-flex-col">
	{#if $poll_title.length && $user_name.length}
	<button on:click={next}>NEXT</button>
	{:else if !$poll_title.length}
		<div class="txt">PLEASE PROVIDE TITLE</div>
	{:else if !$user_name.length}
		<div class="txt">PLEASE PROVIDE YOUR NAME</div>
	{/if}
</p>

</div>

<Footer />

<style>

  .container {
    width: 100vmin;
    height: 80vmin;
    border-radius: 1vmin;
    font-size: var(--font-size-regular);
    box-sizing: border-box;
    padding: 2vmin;
  }

	.table-container {
		width: 80vmin;
	}

	.tbl {
		padding: 0;
		margin: 0;
		border-collapse: collapse;
		width: 100%;
	}

	.tbl td {
		padding-top: 3vmin;
		padding-bottom: 3vmin;
	}

	.txt {
		color: var(--color-accent1);
		padding: 1vmin;
		margin: 1vmin;
	}

</style>
