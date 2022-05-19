<script>

	import { onMount } from 'svelte';
	import { onDestroy } from 'svelte';

	import { gq_get_poll } from '../graphql-client.js';

  import Header from './Header.svelte';
  import Footer from './Footer.svelte';
  import VoteView from './VoteView.svelte';

	import {
		screen,
		poll_id,
		poll_title,
		poll_cname,
		poll_cid,
		poll_notes,
		poll_location,
		poll_videolink,
		poll_timezone,
		poll_participants,
		dates,
		user_name,
		user_id
	} from '../store.js';

	const mode = 'view';
	let interval = false;

	async function load_poll() {
		let poll = await gq_get_poll( $poll_id );
		if ( poll && poll.data && poll.data.getPoll && poll.data.getPoll.pid ) {
			const pd = poll.data.getPoll;
			$poll_id = pd.pid;
			$poll_title = pd.title;
			$poll_cname = pd.cname;
			$poll_cid = pd.cid;
			$poll_notes = pd.notes;
			$poll_location = pd.location;
			$poll_videolink = pd.videolink;
			$poll_timezone = pd.timezone;
			pd.votes.forEach( e => {
				$poll_participants[e.uid] = e;
			});
			$dates = JSON.parse(pd.dates);
		} else {
			// no such poll, redirect with no params
			window.location.href = window.location.pathname
		}
	}

	onMount(async () => {
		load_poll();
		interval = setInterval( () => load_poll(), 30000 );
	});

	onDestroy( () => clearInterval(interval) );

</script>

<Header>
  <span slot="title_center" class="text-center">
    <h2>Meeting Poll</h2><h3>please specify your availability</h3>
  </span>
</Header>

	{#if Object.keys($dates).length}
  <VoteView
		mode={mode}
    dates={$dates}
		poll_id={$poll_id}
    poll_title={$poll_title}
    poll_cid={$poll_cid}
    poll_notes={$poll_notes}
    poll_location={$poll_location}
    poll_videolink={$poll_videolink}
    poll_timezone={$poll_timezone}
    poll_participants={$poll_participants}
		bind:user_name={$user_name}
		user_id={$user_id}
  />
	{:else}
		<p>
		...loading poll...
		</p>
	{/if}

<Footer />