<script>

	import Header from './Header.svelte';
	import Footer from './Footer.svelte';
	import VoteView from './VoteView.svelte';

	import { NotificationDisplay, notifier } from '@beyonk/svelte-notifications';

	import { gq_set_poll, gq_set_vote } from '../graphql-client.js';

  import {
    screen,
		dates,
		poll_id,
    poll_title,
		poll_cid,
    poll_cname,
    poll_notes,
    poll_location,
		poll_videolink,
		poll_participants,
    poll_timezone,
		user_id,
		user_name
  } from '../store.js';

	const mode = 'create';

	function edit() {
		$screen = 'Screen1';
	}

	async function set_poll() {
		let poll = await gq_set_poll(
			$poll_title,
			$user_id,
			$user_name,
			$poll_notes,
			$poll_location,
			$poll_videolink,
			$poll_timezone,
			$dates
		);
		if ( poll && poll.data && poll.data.setPoll && poll.data.setPoll.pid && poll.data.setPoll.pid.length ) {
			let vote = await gq_set_vote(
				// pid, uid, uname, data
				poll.data.setPoll.pid,
				$user_id,
				$user_name,
				$poll_participants[$user_id].data
			);
			if ( vote && vote.data && vote.data.setVote && vote.data.setVote.uid && vote.data.setVote.uid.length ) {
				window.location.href = window.location.pathname + '?id=' + poll.data.setPoll.pid;
			} else {
				notifier.danger('Server-side error, please try again later.', { timeout: 5000 });
			}
		} else {
			notifier.danger('Server-side error, please try again later.', { timeout: 5000 });
		}
	}

</script>

<Header>
	<span slot="title_center" class="text-center-row">
		<h2>Verify and Publish</h2><h3>step 3/3</h3>
	</span>
</Header>

	<NotificationDisplay />

	<VoteView
		mode={mode}
  	poll_id={$poll_id}
  	bind:dates={$dates}
	  bind:poll_title={$poll_title}
	  bind:poll_cid={$poll_cid}
  	bind:poll_notes={$poll_notes}
	  bind:poll_location={$poll_location}
	  bind:poll_videolink={$poll_videolink}
  	bind:poll_timezone={$poll_timezone}
	  bind:poll_participants={$poll_participants}
		bind:user_name={$user_name}
		user_id={$user_id}
		publish={set_poll}
		{edit}
	/>

<Footer />
