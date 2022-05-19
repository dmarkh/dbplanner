<?php
$gqschema = <<<EOD

schema {
  query: Query
  mutation: Mutation
}

type Vote {
  pid:   String!
  uname: String!
  uid:   String!
  ip:    String
	ts:    Int
  data: [Int]
}

type Poll {
	pid:      String!
  title:    String!
  cid:      String
  cname:    String
  notes:    String
  location: String
	videolink: String
  timezone: String!
  dates:    String!
  ip:       String
  ts:       Int
  votes:   [Vote]
}

type Stats {
	npolls: Int!
	nvotes: Int!
}

input VoteInput {
  pid:   String!
  uname: String!
  uid:   String!
	data:  [Int]
}

input PollInput {
  title:    String!
  cid:      String
  cname:    String
  notes:    String
  location: String
	videolink: String
  timezone: String!
  dates:    String!
}

type SetPollPayload {
	pid: String!
}

type SetVotePayload {
	pid: String!
	uid: String!
}

type Query {
	hello: String!
	getPoll( pid: String ) : Poll
	getStats : Stats
}

type Mutation {
	setPoll( poll: PollInput! ) : SetPollPayload
	setVote( vote: VoteInput! ) : SetVotePayload
}

EOD;