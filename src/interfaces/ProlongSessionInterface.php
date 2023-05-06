<?php

namespace safepartner\prolongSession\interfaces;

interface ProlongSessionInterface
{
	/**
	 * @return bool
	 */
	public function isEnabledProlongSession(): bool;

	/**
	 * user logout url
	 * @return string user logout url
	 */
	public function getProlongSessionLogoutUrl(): string;
}