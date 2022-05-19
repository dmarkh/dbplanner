
function uuidv4() {
	return ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c => (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16));
}

function abbr(name) {
	return name.length ? name.match(/\b([a-zA-Z])/g).join('').toUpperCase() : '?';
}

function capitalize_first_letter(str) {
  return str.charAt(0).toUpperCase() + str.slice(1);
}

function array_resize( arr, size = 1, defval = 0 ) {
    while (arr.length > size) { arr.pop(); }
    while (arr.length < size) { arr.push(defval); }
}

function debounce( inputFunc, tm = 500) {
	let timer;
	return (...args) => {
		clearTimeout(timer);
		timer = setTimeout(() => {
			inputFunc.apply(this, args);
		}, tm );
	};
}

function calculate_slots( dates ) {
	return Object.entries( dates ).reduce( (pv, [uuid,cv]) => pv + cv.length, 0 );
}

export { uuidv4, abbr, capitalize_first_letter, array_resize, debounce, calculate_slots }
