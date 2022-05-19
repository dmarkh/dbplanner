<script>

  import TimeDialog from './TimeDialog.svelte';
	import { tick } from "svelte";
	import { calculate_slots } from '../util.js';

  export let dates = {};
  export let next;

  let timedialog = false,
    last_start_tm = '09:00',
    last_start_dur = '60';

	let dts = Object.keys(dates).sort( (a,b) => a.localeCompare(b) );

	let m = dts.length ? moment(dts[0]) : moment(),
		week = m.week(),
		month = m.format('M') | 0,
		year = m.format('YYYY') | 0,
		todays_date = moment().format('YYYY-MM-DD'),
		first_day_of_week = m.clone().subtract( m.day(), 'days' ).format('YYYY-MM-DD'),
		last_day_of_week = m.clone().add( 6 - m.day(), 'days').format('YYYY-MM-DD'),
		prev_day_of_week = m.clone().subtract( m.day() + 1, 'days' ).format('YYYY-MM-DD'),
		next_day_of_week = m.clone().add( 7 - m.day(), 'days').format('YYYY-MM-DD'),
		title = moment(first_day_of_week).format('MMM') + ' ' + moment(first_day_of_week).format('D') 
			+ ' - ' + moment(last_day_of_week).format('MMM') + ' ' + moment(last_day_of_week).format('D');

	let days = [],
		times = [],
		taken_slots = [],
		cols = [0,1,2,3,4,5,6];

	function calculate_taken_slots() {
		let new_taken_slots = [],
			min_time = moment(prev_day_of_week).unix(),
			max_time = moment(next_day_of_week).unix();
		if ( dates ) {
			Object.entries(dates).sort( (a,b) => a[0].localeCompare(b[0]) ).forEach( ([kdt, vdt]) => {
				if ( vdt.length ) {
					vdt.forEach( (val,idx) => {
						if ( moment( kdt ).unix() > min_time && moment(kdt).unix() < max_time ) {
							let m = moment( kdt + ' ' + val.tm );
							new_taken_slots.push({
								kdt,
								tm: val.tm,
								dur: val.dur,
								otop: (moment.duration( val.tm ).asHours() * 9.0),
								oleft: (20.0 + m.day() * 10.0),
								height: val.dur / 60.0 * 9.0,
								begin: m.format('h:mm A'),
								end: m.clone().add( val.dur, 'minutes' ).format('h:mm A'),
								slot: idx
							});
						}
					});
				}
			});
		}
		taken_slots = new_taken_slots;
	}

	async function calculate_days_and_times( dt ) {
		let	m1 = moment(dt).subtract( moment(dt).day(), 'days' ),
			m2 = m1.clone().add( 6, 'days' ), mtmp,
			new_days = [];

		for ( let i = 0; i < 7; i++ ) {
			mtmp = m1.clone().add(i,'days');
			new_days.push(
				{
					'dt': mtmp.format('YYYY-MM-DD'),
					'weekday': mtmp.format('ddd').toUpperCase(),
					'day': mtmp.format('D'),
					'selected': todays_date === mtmp.format('YYYY-MM-DD') ? 'selected' : ''
				}
			);
		}
		let todays_midnight = moment(todays_date + ' 00:00:00'),
			new_times = [];
		for ( let i = 0; i < 24; i++ ) {
			new_times.push({ title: todays_midnight.clone().add( 2*i*30, 'minutes').format('h:mm A'), tm: todays_midnight.clone().add( 2*i*30, 'minutes').format('HH:mm'), style: i%2 == 0 ? 'odd' : 'even' });
			new_times.push({ tm: todays_midnight.clone().add( (2*i+1)*30, 'minutes').format('HH:mm'), style: i%2 == 0 ? 'odd' : 'even' });
		}

		prev_day_of_week = m1.clone().subtract( 1, 'days' ).format('YYYY-MM-DD');
		next_day_of_week = m2.clone().add( 1, 'days').format('YYYY-MM-DD');

		title = m1.format('MMM') + ' ' + m1.format('D') + ' - ' + m2.format('MMM') + ' ' + m2.format('D');
		days = new_days;
		times = new_times;
		calculate_taken_slots();
		await tick();
		scrollTakenSlotsIntoView();
	}

  function add_time_slot(kdt,tm,dur,slot) {
    if ( dates[kdt] ) {
      dates[kdt] = [ ... dates[kdt], { tm, dur: dur | 0 } ];
    } else {
      dates[kdt] = [ { tm, dur: dur | 0 } ];
    }
		last_start_dur = ( typeof dur == 'string' ? dur : dur.toString() );
    timedialog_close();
		calculate_taken_slots();
  }

  function edit_time_slot(kdt,tm,dur,slot) {
    dates[kdt][slot] = { tm, dur: dur | 0 };
    timedialog_close();
    calculate_taken_slots();
  }

  function delete_time_slot(kdt,i) {
    if ( dates[kdt] ) {
      dates[kdt].splice(i,1);
      dates[kdt] = [ ...dates[kdt] ];
      if ( !dates[kdt].length ) {
        dates[kdt] = false;
        delete dates[kdt];
      }
      calculate_taken_slots();
    }
  }

	function timedialog_add_time( dt, tm ) {
		let dur = ( dates[dt] && dates[dt].length ) ? dates[dt].at(-1).dur.toString() : last_start_dur;
    timedialog = {
      op: 0,
      dt,
      tm,
      dur,
      slot: false,
      close: timedialog_close,
      apply: add_time_slot
    };
	}

  function timedialog_edit_time(kdt,i) {
    timedialog = {
      op: 1,
      dt: kdt,
      tm: dates[kdt][i].tm,
      dur: dates[kdt][i].dur.toString(),
      slot: i,
      close: timedialog_close,
      apply: edit_time_slot
    };
  }

	function timedialog_close() {
		timedialog = false;
	}

	function scrollTakenSlotsIntoView() {
		let e = document.getElementsByClassName('taken-slot');
		if ( e && e.length ) {
			let etop = Array.from(e).reduce( (prev, current) => {
    		return (prev.offsetTop < current.offsetTop) ? prev : current
			});
			etop.scrollIntoView({block: "start", inline: "nearest"});
		} else {
			e = document.getElementsByClassName('time-slot-morning');
			e[0].scrollIntoView({block: "start", inline: "nearest"});
		}
	}

	calculate_days_and_times( m.format('YYYY-MM-DD') );

</script>

<div class="left-flex-col w100h80">
<div class="header-slots">
	<table class="tbl">
		<tr>
			<td colspan="8" class="text-left h10">
			<div class="left-flex-row">
				<div class="icon-arrow-back icon icon-rounded theme-hover-light-on-secondary text-large clickable" on:click={calculate_days_and_times(prev_day_of_week)}></div>
				<div class="icon-arrow-forward icon icon-rounded theme-hover-light-on-secondary text-large clickable" on:click={calculate_days_and_times(next_day_of_week)}></div>
				<div class="weekly-title text-regular"> {title} </div>
			</div>
			</td>
		</tr>
		<tr>
			<td class="h10 w20"></td>
			{#each days as day,i}
				<td class="{day.selected} w10">
					<div class="center-flex-col">
						<div class="text-small { ( i == 0 || i == 6 ? "color-accent1": "" ) }">{day.weekday}</div>
						<div class="text-xlarge { ( i == 0 || i == 6 ? "color-accent1": "" ) }">{day.day}</div>
					</div>
				</td>
			{/each}
		</tr>
	</table>
</div>
<div class="time-slots">
	<table class="tbl">
	{#each times as tm,i}
		<tr>
			{#if i%2 == 0 }
			<td class="w20 text-right noborder text-small">
					<div class="{ i == 16 ? 'time-slot-morning':''}" style="margin-top: -3.0vmin; margin-right: 2vmin; font-weight: bold; { ( i < 16 || i > 36 ? "color: var(--font-color-dimmed)": "" ) }">{tm.title}</div>
			</td>
			{:else}
			<td class="w20 noborder">
			</td>
			{/if}
			{#if i%2 == 0 }
				{#each days as day,j}
					<td class="{tm.style} dayst1" 
						on:click|stopPropagation={timedialog_add_time(day.dt, tm.tm)}>
					</td>
				{/each}
			{:else}
				{#each days as day,j}
					<td class="{tm.style} dayst2"
						on:click|stopPropagation={timedialog_add_time(day.dt, tm.tm)}>
					</td>
				{/each}
			{/if}
		</tr>
	{/each}
	</table>

	{#each taken_slots as taken_slot, i}
		<div class="cal-daycell-selected text-xsmall taken-slot" 
			style="top: {taken_slot.otop}vmin; left: {taken_slot.oleft}vmin; height: {taken_slot.height}vmin;" on:click|stopPropagation={timedialog_edit_time(taken_slot.kdt,taken_slot.slot)}>
			<div class="takenslotst" on:click|stopPropagation={delete_time_slot(taken_slot.kdt,taken_slot.slot)}>X</div>
			<div class="center-flex-col text-small takenslotst2">
				<div class="takenslotmargin">{taken_slot.begin}</div>
				<div class="takenslotmargin">{taken_slot.end}</div>
			</div>
		</div>
	{/each}

</div>
<div class="center-flex-col">
	{#if calculate_slots(dates) > 1}
		<button on:click={next}>NEXT</button>
	{:else}
		<p class="color-accent1">PLEASE SELECT DATE(S)</p>
	{/if}
</div>
</div>


{#if timedialog}
<TimeDialog op={timedialog.op} dt={timedialog.dt} tm={timedialog.tm} dur={timedialog.dur} slot={timedialog.slot} close={timedialog.close} apply={timedialog.apply} />
{/if}


<style>

	.left-flex-row {
		display: flex;
		flex-direction: row;
		margin-left: 4vmin;
	}

	.weekly-title {
		font-weight: bold;
		margin-left: 5vmin;
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.tbl {
		width: 90vmin;
		border-collapse: collapse;
    border-style: hidden;
		padding: 0;
		margin: 0;
		box-sizing: border-box;
	}

	.odd {
		background-color: var(--color-paper);
	}

	.even {
		background-color: var(--color-paper-dimmed);
	}

	.tbl td {
    border-left: 0.1vmin solid #999;
    border-right: 0.1vmin solid #999;
		padding: 0;
		margin: 0;
		box-sizing: border-box;
	}

	.tbl tr {
		box-sizing: border-box;
	}

	.header-slots {
		width: 90vmin;
		height: 20vmin;
	}

	.time-slots {
		width: 100vmin;
		height: 52vmin;
		overflow-y: auto;
		overflow-x: hidden;
		position: relative;
	}

	.taken-slot {
		position: absolute;
		width: 10vmin;
		border-left: 0.1vmin solid var(--font-color-light);
		border-top: 0.1vmin solid var(--font-color-light);
	}

	.selected {
		background-color: var(--color-tertiary);
		color: var(--font-color-light);
	}

	.w100h80 {
		width: 100vmin;
		height: 80vmin;
	}

	.h10 {
		height: 10vmin;
	}

	.w10 {
		width: 10vmin;
	}

	.w20 {
		width: 20vmin;
	}

	.dayst1 {
		width: 10vmin;
		height: 4.5vmin;
		border-bottom: 0.1vmin dashed #AAA !important;
	}

	.dayst2 {
		width: 10vmin;
		height: 4.5vmin;
		border-bottom: 0.1vmin solid #999 !important;
	}

	.takenslotst {
		position: absolute;
		top: 0;
		right: 0;
		cursor: pointer;
		padding: 0.2vmin;
		border: 1px solid #CCC;
	}

	.takenslotst2 {
		height: 100%;
		width: 100%;
		align-items: flex-end;
	}

	.takenslotmargin {
		margin-right: 0.8vmin;
	}

</style>