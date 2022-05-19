<script>

	import TimeDialog from './TimeDialog.svelte';

	export let month;
	export let year;
	export let dates = {};
	export let next;

	let timedialog = false,
		last_start_tm = '09:00',
		last_start_dur = '60',
		dts = Object.keys(dates).sort( (a,b) => a.localeCompare(b) ),
		weekdays = [];

  let m = dts.length ? moment(dts[0]) : moment();
  month = month ? month : m.format('M') | 0;
  year  = year ? year : m.format('YYYY') | 0;

	let todays_date = moment().format('YYYY-MM-DD'),
		title = '',
		days_to_display = [];

	for ( let i = 0; i < 7; i++ ) {
		weekdays.push( moment('2022-05-01').add(i,'days').format('ddd').toUpperCase() );
	}

	function calculate_days_to_display() {
		days_to_display = [];
		let m = moment({ y: year, M: month - 1, d: 1 });
		title = m.format('MMMM') + ' ' + m.format('YYYY');
		// subtract offset for display purposes
    m.subtract( m.day() == 0 ? 7 : m.day(), 'days' ); 
		for ( let i = 0; i < 42; i++ ) {
			days_to_display.push({
				id: i,
				dow: m.day(),
				day: m.format('D'),
				month: m.format('M'),
				year: m.format('YYYY'),
				date: m.format('YYYY-MM-DD'),
				today: todays_date === m.format('YYYY-MM-DD'),
				out: m.format('M') != month,
				sel: !!dates[ m.format('YYYY-MM-DD') ]
			});
			m.add( 1,'day' );
		}
		return days_to_display;
	}

	function move_back_one_month() {
		month |= 0; year |= 0;
 		month -= 1;
		if ( month < 1 ) { month = 12; year -= 1; }
		calculate_days_to_display();
	}

	function move_forward_one_month() {
		month |= 0; year |= 0;
		month += 1;
		if ( month > 12 ) { month = 1; year += 1; }
		calculate_days_to_display();
	}

	function toggle_day_selection(dt) {
		if ( !dates[dt] ) {
			timedialog_add_time( dt );
		} else {
			dates[dt] = false;
			delete dates[dt];
			calculate_days_to_display();
		}
	}

	function delete_time_slot(kdt,i) {
		if ( dates[kdt] ) {
			dates[kdt].splice(i,1);
			dates[kdt] = [ ...dates[kdt] ];
			if ( !dates[kdt].length ) {
				dates[kdt] = false;
				delete dates[kdt];
			}
			calculate_days_to_display();
		}
	}

	function add_time_slot(kdt,tm,dur,slot) {
		if ( dates[kdt] ) {
			dates[kdt] = [ ... dates[kdt], { tm, dur: dur | 0 } ];
		} else {
			dates[kdt] = [ { tm, dur: dur | 0 } ];
		}
		last_start_tm = tm;
		last_start_dur = ( typeof dur == 'string' ? dur : dur.toString() );
		timedialog_close();
		calculate_days_to_display();
	}

	function edit_time_slot(kdt,tm,dur,slot) {
		dates[kdt][slot] = { tm, dur: dur | 0 };
		timedialog_close();
		calculate_days_to_display();
	}

	function timedialog_close() {
		timedialog = false;
	}

	function timedialog_add_time(kdt) {
		let tm = ( dates[kdt] && dates[kdt].length ) ? moment(kdt + ' ' + dates[kdt].at(-1).tm ).add( dates[kdt].at(-1).dur, 'minutes').format('HH:mm') : last_start_tm,
			dur = ( dates[kdt] && dates[kdt].length ) ? dates[kdt].at(-1).dur.toString() : last_start_dur;
		timedialog = {
			op: 0,
			dt: kdt,
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

	function get_date_name( kdt ) {
		let m = moment(kdt);
		return m.format('ddd').toUpperCase() + ', ' + m.format('MMM').toUpperCase() + ' ' + m.format('DD');
	}

	function get_slot_name( kdt, slot ) {
		let m = moment( kdt + ' ' + slot.tm );
		return m.format('h:mm A') + ' - ' + m.add( slot.dur, 'minutes').format('h:mm A');
	}

	calculate_days_to_display( month, year );

</script>

<div class="cal-left">
	<div class="cal">
		<div class="cal-header">
			<div class="cal-header-left">
				<div class="icon-arrow-back icon icon-rounded theme-hover-light-on-secondary text-large clickable" on:click={move_back_one_month}/>
			</div>
			<div class="cal-header-title">{title}</div>
			<div class="cal-header-right">
				<div class="icon-arrow-forward icon icon-rounded theme-hover-light-on-secondary text-large clickable" on:click={move_forward_one_month}/>
			</div>
		</div>
		<div class="cal-main">
			{#each weekdays as weekday,i}
				<div class="day-cell center-flex-col">
					<div class="cal-weekcell { ( i == 0 || i == 6 ? "color-accent1": "" ) }">
						{weekday}
					</div>
				</div>
			{/each}
			{#each days_to_display as day}
				<div class="day-cell center-flex-col">
					<div class="cal-daycell cal-daycell-{day.id} {day.sel ? 'cal-daycell-selected' : '' } {day.today ? 'cal-daycell-today' : ''} {day.out ? 'cal-daycell-out' : ''}" on:click={() => toggle_day_selection(day.date)} data-date="{day.date}" data-m="{day.month}" data-yr="{day.year}">
						{day.day}
					</div>
				</div>
			{/each}
		</div>
		<div class="cal-footer">
			{#if Object.keys(dates).length > 1}
				<button on:click={next}>NEXT</button>
			{:else}
				PLEASE SELECT DATE(S)
			{/if}
		</div>
	</div>
</div>
<div class="cal-right paper-dimmed">
	{#each Object.entries(dates).sort( (a,b) => a[0].localeCompare(b[0]) ) as [kdt, vdt]}
		{#if vdt.length}
		<div class="cal-day-slot" data-dt={kdt}>
			<div class="text-bold">
				{ get_date_name( kdt ) }
			</div>
			<div class="cal-day-slot-item paper">
			{#each vdt as slot,i}
				<div>
					<div class="cal-day-slot-item-dt" on:click={timedialog_edit_time(kdt,i)}>{ get_slot_name( kdt, slot ) }</div>
					<div class="cal-day-slot-item-del" on:click={delete_time_slot(kdt,i)}>X</div>
				</div>
			{/each}
			<div class="cal-day-slot-item-add" on:click={timedialog_add_time(kdt)}>+ add more times</div>
			</div>
		</div>
		{/if}
	{/each}
</div>
{#if timedialog}
<TimeDialog op={timedialog.op} dt={timedialog.dt} tm={timedialog.tm} dur={timedialog.dur} slot={timedialog.slot} close={timedialog.close} apply={timedialog.apply} />
{/if}

<style>
	.cal-left {
		width: 70%;
		height: 100%;
	}
	.cal-right {
		width: 30%;
		height: 100%;
		scrollbar-width: thin;
		overflow-y: auto;
	}
	.cal-day-slot-item-dt {
		width: 90%;
		display: inline-block;
		cursor: pointer;
	}
	.day-cell {
		width: 100%;
		height: 100%;
	}
</style>