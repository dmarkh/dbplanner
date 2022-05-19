<script>

	import { vote_states } from '../constants.js';
	import { uuidv4, abbr, array_resize, debounce, calculate_slots } from '../util.js';
	import timezones from '../timezones.js';
	import SelectInput from './SelectInput.svelte';
	import TextInput from './TextInput.svelte';

	import { NotificationDisplay, notifier } from '@beyonk/svelte-notifications';

	import { gq_set_vote } from '../graphql-client.js';

	export let mode = 'create'; // 'create' or 'view'

  export let dates = {};
	export let poll_id = false;
  export let poll_title = '';
  export let poll_cid = '';
  export let poll_notes = '';
  export let poll_location = '';
	export let poll_videolink = '';
  export let poll_timezone = '';
  export let poll_participants = {};

	export let user_id = '';
	export let user_name = '';

  export let edit = false;
  export let publish = false;

	let op = '',
		poll_timezone_preview_value = ( poll_timezone != moment.tz.guess(true) ) ? moment.tz.guess(true) : poll_timezone,
		poll_timezone_preview_options = ( poll_timezone != moment.tz.guess(true) ? 
		[ poll_timezone, moment.tz.guess(true), '----------', ...timezones ] : [ poll_timezone, '----------', ...timezones ] ),
		date_rows = [],
		time_rows = [],
		dts = Object.keys( dates ).sort(),
    slots = calculate_slots( dates ),
		participant_vote_count = new Array(slots).fill(0),
		is_past_deadline = moment( Object.keys( dates ).sort().pop() ).unix() < moment().unix();

	if ( !Object.keys( poll_participants ).length ) {
		poll_participants = {
			[user_id]: {
				uname: user_name,
				data: new Array(slots).fill(0)
			}
		};
	} else {
		if ( !poll_participants[user_id] ) {
			poll_participants[user_id] = {
				uname: user_name,
				data: new Array(slots).fill(0)
			};
		}
		// update number of slots
		Object.entries( poll_participants ).forEach(([ uuid, participant ]) => {
			array_resize( participant.data, slots, 0 );
		});
	}

	function recalculate_vote_counts() {
		let tmp_participant_vote_count = new Array(slots).fill(0);
		Object.entries( poll_participants ).forEach(([ uuid, participant ]) => {
			participant.data.forEach( (vote, idx) => {
				if ( vote == 1 || vote == 2 ) { tmp_participant_vote_count[ idx ] += 1; }
			});
		});
		participant_vote_count = tmp_participant_vote_count;
	}

	function cycle_vote( participant, slot ) {
		poll_participants[ participant ].data[ slot ] += 1;
		if ( poll_participants[ participant ].data[ slot ] > 3 ) {
			poll_participants[ participant ].data[ slot ] = 1;
		}
		recalculate_vote_counts();
		save_user_vote();
	}

	async function save_user_vote() {
		if ( !poll_id || !poll_id.length || !user_name.length ) { return; }
		await gq_set_vote( poll_id, user_id, user_name, poll_participants[user_id].data );
	}

	function build_date_time_rows( value ) {
		let tzdates = {},
			tzzone = poll_timezone_preview_value;

		if ( !moment.tz.zone( tzzone ) ) {
			notifier.warning('Unknown timezone encountered', { timeout: 5000 });
			return;
		}

		let new_date_rows = [], new_time_rows = [], new_slots = 0;

		for ( let i = 0; i < dts.length; i++ ) {
  	 	for ( let j = 0; j < dates[dts[i]].length; j++ ) {
				let m = moment.tz( dts[i] + ' ' + dates[dts[i]][j].tm, poll_timezone ).clone().tz( tzzone ),
					dt = m.format('YYYY-MM-DD');
				if ( !tzdates[dt] ) { tzdates[dt] = []; }
				tzdates[dt].push({ tm: m.format('HH:mm'), dur: dates[dts[i]][j].dur });
				new_slots += 1;
			}
		}

		let tzdts = Object.keys( tzdates ).sort();

		// create date and time rows
		for ( let i = 0; i < dts.length; i++ ) {
			let s = tzdates[ tzdts[i]].length,
				m = moment.tz( tzdts[i], tzzone ),
				date_row = {
					slots: s,
					month: m.format('MMM').toUpperCase(),
					day: m.format('D'),
					weekday: m.format('ddd').toUpperCase()
				};
			new_date_rows.push( date_row );
 			for ( let j = 0; j < s; j++ ) {
				let m = moment.tz( tzdts[i] + ' ' + tzdates[ tzdts[i] ][j].tm, tzzone ),
					time_row = {
						start: m.format('h:mm A'),
						stop: m.add( tzdates[ tzdts[i] ][j].dur,'minutes' ).format('h:mm A')
					};
				new_time_rows.push( time_row );
			}
		}

		slots = new_slots;
		date_rows = new_date_rows;
		time_rows = new_time_rows;
	}

	function copy_link_to_clipboard() {
		navigator.clipboard.writeText( window.location.href );
		notifier.info('The link has been copied to the clipboard', { timeout: 5000 });
	}

	build_date_time_rows();
	recalculate_vote_counts();

</script>

<NotificationDisplay />
<div class="view-table-wrapper">
	<div class="view-table-wrapper-inner paper poswidth">

		<table class="text-regular view-table">
    	<tbody>

				<!-- info row //-->
				<tr>
          <td colspan={slots+1} class="pad5vmin">
						{#if poll_title.length}
            <p class="text-xlarge text-bold text-left nomarginborder">
              {poll_title}
            </p>
						{/if}
						{#if poll_cid != user_id }
            <p>
              <span class="abbr">{abbr(poll_participants[poll_cid].uname)}</span> <span class="font-bold">{poll_participants[poll_cid].uname}</span> has invited you to choose the best times for this meeting
            </p>
						{:else}
            <p>
              <span class="abbr">{abbr(user_name)}</span> <span class="font-bold">{user_name}</span> has invited you to choose the best times for this meeting
            </p>
						{/if}
						<div class="left-flex-col">
	            {#if poll_notes.length}
  	          <div class="center-flex-row">
    	          <div class="icon-notes text-regular offset"></div>
      	        <div class="width50"> {poll_notes} </div>
        	    </div>
          	  {/if}
	            {#if poll_location.length}
 		          <div class="center-flex-row margintop2vmin">
   		          <div class="icon-location text-regular offset"></div>
     		        <div class="width50"> {poll_location} </div>
       		    </div>
         		  {/if}
           		{#if poll_timezone.length}
            	<div class="center-flex-row margintop2vmin">
								<div class="icon-timezone text-regular offset"></div>
								<div> <SelectInput onchange={build_date_time_rows} id="poll_timezone_preview" placeholder="Timezone Preview" bind:value={poll_timezone_preview_value} options={poll_timezone_preview_options} /> </div>
 	  	        </div>
  	          {/if}
	            {#if poll_videolink.length}
 		          <div class="center-flex-row margintop2vmin">
   		          <div class="icon-videolink text-regular offset"></div>
								{#if poll_videolink && poll_videolink.toLowerCase().startsWith('http')}
     		        <div> <a class="smartlink paper nopad noborder nomargin" style="font-weight: normal;" href="{poll_videolink}" target="_blank">{poll_videolink}</a> </div>
								{:else}
     		        <div class="paper nopad noborder nomargin">{poll_videolink}</div>
								{/if}
       		    </div>
         		  {/if}
						</div>
					</td>
				</tr>

				<tr class="text-center-row">
 					<td align="center" valign="center">

						<div class="left-flex-col text-large">
							<div class="text-small votemargin"> <div class="vote-yes hint"></div> yes </div>
							<div class="text-small votemargin"> <div class="vote-maybe hint"></div> if need be </div>
							<div class="text-small votemargin"> <div class="vote-no hint"></div> no </div>
							<div class="text-small votemargin"> <div class="vote-unknown hint"></div> pending </div>
						</div>

					</td>
					{#each date_rows as row}
					<td colspan={row.slots}>
          	<div class="text-small">{row.month}</div>
          	<div class="text-xlarge">{row.day}</div>
         	 <div class="text-small">{row.weekday}</div>
        	</td>
					{/each}
    		</tr>

				<!-- time rows //-->
				<tr class="text-center-row view-table-row-border">
					<td></td>
					{#each time_rows as row}
					<td align="center" class="timeminwidth">
            <div class="text-small text-right timemargin">
							{row.start}
							<br/>
							{row.stop}
						</div>
        	</td>
					{/each}
				</tr>

				<!-- participant count row //-->
				<tr class="text-center-row view-table-row-border participantheight">
					<td class="participantalign">Participants</td>
					{#each participant_vote_count as vote}
						<td class="part-row">
							<div class="icon-group text-large particon"></div>
							<div class="text-regular partvote">{vote}</div>
						</td>
					{/each}
				</tr>

				<!-- participants and votes //-->

				<!-- current user goes first //-->
				{#if !is_past_deadline || poll_participants[user_id]}
				<tr class="text-center-row view-table-row-border">
					<td class="participant-name">
							<div class="abbr">{abbr(user_name)}</div>
							{#if is_past_deadline}
									<div class="participant">{user_name}</div>
							{:else}
							<div class="participant">
									<TextInput id="user_name" placeholder="your name" bind:value={user_name} oninput={debounce(save_user_vote)} />
								{#if user_id === poll_cid}
									<div class="poll-organizer">Organizer</div>
								{/if}
							</div>
							{/if}
					</td>
					{#each poll_participants[user_id].data as vote,slot}
					<td class="nopad">
						{#if is_past_deadline}
							<div class="{vote_states[vote]} text-xxlarge"></div>
						{:else}
							<div class="{vote_states[vote]} text-xxlarge clickable" on:click={cycle_vote(user_id,slot)}></div>
						{/if}
					</td>
					{/each}
				</tr>
				{/if}

				<!-- remaining users //-->
				{#each Object.entries(poll_participants) as [uuid,participant]}
				{#if uuid !== user_id}
				<tr class="text-center-row view-table-row-border">
					<td class="participant-name">
						<div class="abbr">{abbr(participant.uname)}</div>
						<div class="participant">{participant.uname}</div>
						{#if uuid === poll_cid}
							<div class="poll-organizer">Organizer</div>
						{/if}
					</td>
					{#each participant.data as vote,slot}
					<td class="nopad">
						<div class="{vote_states[vote]} text-xxlarge"></div>
					</td>
					{/each}
				</tr>
				{/if}
				{/each}

			</tbody>
		</table>

		{#if mode == 'create'}
    <p>
      <button on:click={edit}>EDIT DETAILS</button>
      &nbsp;&nbsp;&nbsp;&nbsp;
			{#if !is_past_deadline}
      <button on:click={publish}>PUBLISH!</button>
			{:else}
      CANNOT PUBLISH, DATES ARE IN THE PAST
			{/if}
    </p>
		{:else if mode === 'view' && op == 'saving'}
			<p>saving data...</p>
		{:else if mode === 'view' && poll_cid === user_id}
			<p class="margintop5vmin">
				<a class="smartlink" href="mailto:?subject={poll_title}&body=Hi All,%0A%0AI'm trying to see when we're all available for: {poll_title}%0A%0APlease visit%0A  {window.location.href}%0A%0Ato let me know when you're available.%0A%0AThanks,%0A%0A">SHARE VIA EMAIL</a>
				<span class="smartlink" on:click={copy_link_to_clipboard}>COPY LINK</span>
			</p>
			<p>
				{window.location.href} 
			</p>
		{/if}

  </div>
</div>

<style>

	.left-flex-col {
		display: flex;
		flex-direction: column;
		align-items: flex-start;
		justify-content: flex-start;
	}

	.offset {
		margin-right: 2vmin;
	}

	.part-row {
		height: 5vmin;
		vertical-align: middle;
		align: center;
		padding-top: 0;
		padding-bottom: 0;
	}

	.abbr {
  	display: inline-block;
	  background-color: var(--color-primary);
  	color: var(--font-color-light);
	  font-size: var(--font-size-small);
  	border-radius: 50%;
	  text-align: center;
  	padding: 1vmin;
	  margin-right: 1vmin;
  	min-width: calc(1.2*var(--font-size-small));
	}

	.participant {
  	display: inline-block;
		margin-right: 1vmin;
		text-align: left;
	}

.vote-unknown {
  background-color: var(--color-vote-unknown);
}

.vote-unknown::before {
    font-family: 'Material Icons';
    content: 'help';
}

.vote-yes {
  background-color: var(--color-vote-yes);
}

.vote-yes::before {
    font-family: 'Material Icons';
    content: 'task_alt';
}
.vote-no {
  background-color: var(--color-vote-no);
}

.vote-no::before {
    font-family: 'Material Icons';
    content: 'block'
}

.vote-maybe {
  background-color: var(--color-vote-maybe);
}

.vote-maybe::before {
    font-family: 'Material Icons';
    content: 'published_with_changes'
}

.hint {
	display: inline-block;
	width: 10vmin;
	text-align: center;
}

.participant-name {
	display: flex;
	align-items: center;
	justify-content: flex-start;
	height: 8vmin;
	position: relative;
}

.poll-organizer {
	font-size: var(--font-size-xsmall);
	position: absolute;
	bottom: 1vmin;
	right: 1vmin;
}

.poswidth {
	position: relative;
	min-width: 90vmin;
}

.pad5vmin {
	padding-bottom: 5vmin;
}

.margintop2vmin {
	margin-top: 2vmin;
}

.margintop5vmin {
	margin-top: 5vmin;
}

.votemargin {
	margin-right: 2vmin;
	display: inline-block;
}

.timeminwidth {
	min-width: 15vmin;
}

.timemargin {
	margin-top: 1vmin;
	width: min-content;
	white-space: nowrap;
}

.participantheight {
	height: 5vmin;
}

.participantalign {
	text-align: left;
	height: 5vmin;
	padding-top: 0;
	padding-bottom: 0;
}

.particon {
	display: inline-block;
	vertical-align: middle;
}

.partvote {
	display: inline-block;
	margin-left: 1vmin;
	vertical-align: middle;
}

</style>
