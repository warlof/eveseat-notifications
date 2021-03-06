<?php

/*
 * This file is part of SeAT
 *
 * Copyright (C) 2015, 2016, 2017  Leon Jacobs
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

namespace Seat\Notifications\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

/**
 * Class MemberApiState.
 * @package Seat\Notifications\Notifications
 */
class MemberApiState extends Notification
{
    /**
     * @var
     */
    private $member;

    /**
     * Create a new notification instance.
     *
     * @param $member
     */
    public function __construct($member)
    {

        $this->member = $member;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {

        return $notifiable->notificationChannels();
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

        return (new MailMessage)
            ->success()
            ->greeting('Heads up!')
            ->line(
                'A corporation members API key state has changed!'
            )
            ->line(
                $this->member->name . '\'s API is now ' .
                $this->member->enabled ? 'enabled' : 'disabled' . '!'
            )
            ->action('Check it out on SeAT', route('api.key.detail', [
                'key_id' => $this->member->keyID,
            ]));
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param $notifiable
     *
     * @return $this
     */
    public function toSlack($notifiable)
    {

        return (new SlackMessage)
            ->error()
            ->content('A corporation members API key state has changed!')
            ->attachment(function ($attachment) {

                $attachment->title('Key Details', route('api.key.detail', [
                    'key_id' => $this->member->keyID,
                ]))->fields([
                    'Character Name'        => $this->member->name,
                    'Character Corporation' => $this->member->corporationName,
                    'New Key State'         => $this->member->enabled ? 'Enabled' : 'Disabled',
                ]);
            });
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {

        return [
            'character_name'        => $this->member->name,
            'character_corporation' => $this->member->corporationName,
            'new_key_state'         => $this->member->enabled ? 'Enabled' : 'Disabled',
        ];
    }
}
