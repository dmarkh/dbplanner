<script>

  moment.updateLocale('en', { week : { dow : 0, doy : 6 } }); // enforce US locale
  moment.locale('en');

	import { screen, user_id, user_name } from './store.js';

	import Screen0 from './components/Screen0.svelte';
	import Screen1 from './components/Screen1.svelte';
	import Screen2 from './components/Screen2.svelte';
	import Screen3 from './components/Screen3.svelte';
	import Screen4 from './components/Screen4.svelte';

</script>

	{#if $screen == 'Screen0'}
		<Screen0 />
	{:else if $screen == 'Screen1'}
		<Screen1 />
	{:else if $screen == 'Screen2'}
		<Screen2 />
	{:else if $screen == 'Screen3'}
		<Screen3 />
	{:else if $screen == 'Screen4'}
		<Screen4 />
	{/if}
