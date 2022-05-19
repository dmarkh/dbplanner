<script>

	import { time_operations } from '../constants.js';

	export let op = 0;
	export let dt = 'YYYY-MM-DD';
	export let tm = '09:00';
	export let dur = 60;
	export let slot = false;

	export let apply;
	export let close;

	let m = moment(dt),
		date_str = m.format('ddd').toUpperCase() + ', ' + m.format('MMM').toUpperCase() + ' ' + m.format('D') + ', ' + m.format('YYYY'),
		op_str = time_operations[op];

</script>

<div id="cal-popup">
	<div class="cal-popup-shade"></div>
	<div class="cal-popup-paper">
		<table class="dialog-tbl">
			<tr><td colspan=2><div class="center-flex-row width100p"> {date_str} </div></td></tr>
			<tr><td colspan=2><div class="text-small center-flex-row width100p marginbottom2vmin"> {op_str} </div> </td>
			<tr>
				<td align="right">Time Slot:</td>
				<td>
					<input type="time" id="cal-popup-time" name="cal-popup-time" bind:value={tm}>
				</td>
			</tr>
			<tr>
				<td align="right">Duration:</td>
				<td>
					<select name="cal-popup-duration" id="cal-popup-duration" bind:value={dur}>
						<option value="5">0:05</option>
						<option value="15">0:15</option>
						<option value="30">0:30</option>
						<option value="45">0:45</option>
						<option value="60">1:00</option>
						<option value="90">1:30</option>
						<option value="120">2:00</option>
						<option value="150">2:30</option>
						<option value="180">3:00</option>
						<option value="240">4:00</option>
						<option value="300">5:00</option>
						<option value="360">6:00</option>
						<option value="720">12:00</option>
						<option value="1440">24:00</option>
					</select>
					<span class="text-small">h:mm</span>
				</td>
			</tr>
		</table>
		<p class="margintop3vmin">
			<button on:click={close}>CANCEL</button> 
			<button on:click={apply(dt,tm,dur,slot)}>APPLY</button>
		</p>
	</div>
</div>

<style>
	.dialog-tbl td {
		padding: 1vmin;
	}
	.marginbottom2vmin {
		margin-bottom: 2vmin;
	}
	.margintop3vmin {
		margin-top: 3vmin;
	}
</style>