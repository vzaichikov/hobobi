<?php

namespace hobotix;

class Telegram{
	private $telegramAPI;
	private $chatID;

	public function __construct($apiKey, $botName, $chatID){
		try {
			$this->telegramAPI = new \Longman\TelegramBot\Telegram($apiKey, $botName);
			$this->chatID = $chatID;
		} catch (\Longman\TelegramBot\Exception\TelegramException $e) {
			echoLine($e->getMessage());
		}
	}


	public function sendMessage($message){
		try {
			$result = \Longman\TelegramBot\Request::sendMessage([
				'chat_id' => $this->chatID,
				'text'    => $message,
				'parse_mode' => 'HTML',
			]);
		} catch (\Longman\TelegramBot\Exception\TelegramException $e) {
			echoLine($e->getMessage());
		}
	}




}