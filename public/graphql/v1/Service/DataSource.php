<?php

namespace Service;

use \PDO;

class DataSource {

	private static $DB = [
		'dsn' => 'mysql:host=localhost;dbname=dbplanner',
		'user' => 'dbplanner',
		'pass' => 'dbplanner'
	];

	private static $dbh = false;

	public static function init() {
		if ( !self::$dbh ) {
			try {
				self::$dbh = new PDO( self::$DB['dsn'], self::$DB['user'], self::$DB['pass'] );
				self::$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			} catch ( PDOException $e ) {
				echo $e->getMessage();
				die();
			}
		}
	}

	public static function getPoll( $pid ) {
		$res = self::database_query( 'SELECT * FROM polls WHERE pid = :pid', [ 'pid' => self::sanitize_input_string($pid) ] );
		return $res[0] ?? [];
	}

	public static function getStats() {
		$res1 = self::database_query( 'SELECT COUNT(*) AS npolls FROM polls WHERE 1' );
		$res2 = self::database_query( 'SELECT COUNT(*) AS nvotes FROM votes WHERE 1' );
		return [ 'npolls' => $res1[0]['npolls'], 'nvotes' => $res2[0]['nvotes'] ];
	}

	public static function getVotes( $pid ) {
		$res = self::database_query( 'SELECT * FROM votes WHERE pid = :pid', [ 'pid' => self::sanitize_input_string($pid) ] );
		foreach( $res as $k => $v ) {
			foreach( $v as $k2 => $v2 ) {
				if ( $k2 == 'data' ) {
					$res[$k][$k2] = json_decode($v2);
				}
			}
		}
		return $res ?? [];
	}

	public static function setPoll( $title, $cid, $cname, $notes, $location, $videolink, $timezone, $dates ) {
		$pid = self::uuid();
		$ip = self::get_ip();
		$ts = time();

		$res = self::database_query( 'INSERT INTO polls ( pid, title, cid, cname, notes, location, videolink, timezone, dates, ip, ts ) VALUES ( :pid, :title, :cid, :cname, :notes,  :location, :videolink, :timezone, :dates, :ip, :ts )',
			[ 'pid' => $pid, 'title' => $title, 'cid' => $cid, 'cname' => $cname, 'notes' => $notes,
				 'location' => $location, 'videolink' => $videolink, 'timezone' => $timezone, 'dates' => $dates, 'ip' => $ip, 'ts' => $ts ]);

		return $res === null ? [ 'pid' => '' ] : [ 'pid' => $pid ];
	}

	public static function setVote( $pid, $uid, $uname, $data ) {

		$ip = self::get_ip();
		$ts = time();

		if ( str_starts_with( self::$DB['dsn'], 'mysql' ) ) {

			$res = self::database_query( 'REPLACE INTO votes ( pid, uid, uname, data, ip, ts ) VALUES ( :pid, :uid, :uname, :data, :ip, :ts )',
				[ 'pid' => $pid, 'uid' => $uid, 'uname' => $uname, 'data' => json_encode($data), 'ip' => $ip, 'ts' => $ts ]);

			return $res === null ? [ 'pid' => '', $uid => '' ] : [ 'pid' => $pid, 'uid' => $uid ];

		} else if ( str_starts_with( self::$DB['dsn'], 'pgsql' ) ) {

			$res = self::database_query('INSERT INTO votes ( pid, uid, uname, data, ip, ts ) values ( :pid, :uid, :uname, :data, :ip, :ts ) ON CONFLICT ( pid, uid ) DO UPDATE SET data = :data',
				[ 'pid' => $pid, 'uid' => $uid, 'uname' => $uname, 'data' => json_encode($data), 'ip' => $ip, 'ts' => $ts ]);

			return $res === null ? false : true;

		} else {
			error_log("Database type is not supported: " . self::$DB['dsn'], 0);
			exit();
		}

		return false;
	}

	private static function database_query( $sql, $params = [] ) {
		self::init();
		try {
			if ( count($params) ) {
				$sth = self::$dbh->prepare( $sql );
				$sth->execute( $params );
			} else {
				$sth = self::$dbh->query($sql);
			}
			$res = $sth->fetchAll( PDO::FETCH_ASSOC );
			$sth = null;
			$dbh = null;
			return $res;
		} catch ( PDOException $e ) {
			error_log( $e->getMessage(), 0);
		}
		return null;
	}

	private static function uuid() {
		$data = random_bytes(16);
		$data[6] = chr(ord($data[6]) & 0x0f | 0x40);
		$data[8] = chr(ord($data[8]) & 0x3f | 0x80);
		return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
	}

  private static function sanitize_input_string( $str ) {
    // filters out everything but UTF letters, numbers and " _-.,!?"
    return preg_replace( '/[^\pL\pN\040\._:?!,-]+/u', '', $str );
  }

	private static function get_ip() {
		return $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
	}

}
