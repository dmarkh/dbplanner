
const url = "http://127.0.0.1/dbPlanner3/graphql/v1/";

async function gq_make_request( url, query = '', variables = {} ) {
	return fetch( url, {
		referrerPolicy: "no-referrer",
		cache: "no-store",
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			'Cache-Control': 'no-cache'
		},
		body: JSON.stringify({
			query,
			variables
		}),
	})
		.then((res) => res.json());
}

async function gq_get_poll( pid ) {
	let query = `
	query getPoll( $pid: String ) {
		getPoll( pid: $pid ) {
			pid
			cid
			title
			cname
			notes
			location
			videolink
			timezone
			dates
			votes {
				pid
				uid
				uname
				data
			}
		}
	}
	`;
	return gq_make_request( url, query, { pid } );
}

async function gq_set_poll( title, cid, cname, notes, location, videolink, timezone, dates = {} ) {
	let query = `
		mutation setPoll( $poll: PollInput! ) {
  		setPoll( poll: $poll ) {
	    	pid
  		}
		}
	`;
	return gq_make_request( url, query,
		{
			poll: {
				title,
				cid,
				cname,
				notes,
				location,
				videolink,
				timezone,
				dates: JSON.stringify(dates)
			}
		}
	);
}

async function gq_set_vote( pid, uid, uname, data ) {
	let query = `
		mutation setVote( $vote: VoteInput! ) {
			setVote( vote: $vote ) {
				pid
				uid
			}
		}
	`;
	return gq_make_request( url, query,
		{
			'vote': {
				pid,
				uid,
				uname,
				data
			}
		}
	);
}

async function gq_get_stats() {
	let query = `
	query getStats {
		getStats {
			npolls
			nvotes
		}
	}
	`;
	return gq_make_request( url, query, {});
}

export { gq_make_request, gq_get_poll, gq_set_poll, gq_set_vote, gq_get_stats };
