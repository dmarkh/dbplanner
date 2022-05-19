
import { writable } from 'svelte/store';
import { uuidv4, get_pid } from './util.js';

// theme state
export const theme = writable( localStorage.getItem('theme') ?? 'theme-light' );
document.documentElement.className = theme;
theme.subscribe( value => {
	document.documentElement.className = value;
	localStorage.setItem('theme', value);
});

// calendar state: weekly, monthly
export const calendarview = writable('weekly');

// poll state
export const poll_id = writable( ( new URLSearchParams(window.location.search) ).get('id') );
export const poll_title = writable('');
export const poll_cname = writable(false);
export const poll_cid = writable(false);
export const poll_notes = writable('');
export const poll_location = writable('');
export const poll_videolink = writable('');
export const poll_timezone = writable( undefined );
export const poll_participants = writable({});
export const dates = writable({});

// user state
export let user_name = writable( localStorage.getItem('user_name') ?? '' );
user_name.subscribe( value => {
	localStorage.setItem('user_name', value);
});

if ( !localStorage.getItem('user_id') ) {
	localStorage.setItem('user_id', uuidv4());
}
export let user_id = writable( localStorage.getItem('user_id') ?? '' );
user_id.subscribe( value => {
	localStorage.setItem('user_id', value);
});

// app screen state
export const screen = ( new URLSearchParams(window.location.search) ).get('id') ? writable('Screen4') : writable('Screen0');
