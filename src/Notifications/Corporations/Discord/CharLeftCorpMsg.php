<?php

/*
 * This file is part of SeAT
 *
 * Copyright (C) 2015 to 2020 Leon Jacobs
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

namespace Seat\Notifications\Notifications\Corporations\Discord;

use Seat\Eveapi\Models\Universe\UniverseName;
use Seat\Notifications\Jobs\AbstractCharacterNotification;
use Seat\Notifications\Services\Discord\Messages\DiscordEmbed;
use Seat\Notifications\Services\Discord\Messages\DiscordEmbedField;
use Seat\Notifications\Services\Discord\Messages\DiscordMessage;

/**
 * Class CharLeftCorpMsg
 *
 * @package Seat\Notifications\Notifications\Corporations\Discord
 */
class CharLeftCorpMsg extends AbstractCharacterNotification
{
    /**
     * @inheritDoc
     */
    public function via($notifiable)
    {
        return ['discord'];
    }

    /**
     * @param $notifiable
     *
     * @return \Seat\Notifications\Services\Discord\Messages\DiscordMessage
     */
    public function toDiscord($notifiable)
    {
        return (new DiscordMessage())
            ->content('A character has left corporation!')
            ->embed(function (DiscordEmbed $embed) {
                $embed->timestamp($this->notification->timestamp);
                $embed->author(
                    'SeAT Corporation Supervisor',
                    asset('web/img/favico/apple-icon-180x180.png'),
                    route('corporation.view.default', ['corporation' => $this->notification->text['corpID']])
                );

                $embed->field(function (DiscordEmbedField $field) {
                    $corporation = UniverseName::find($this->notification->text['corpID']) ?? trans('web::seat.unknown');

                    $field->name('Corporation')
                        ->value($corporation->name)
                        ->long();
                });

                $embed->field(function (DiscordEmbedField $field) {
                    $character = UniverseName::find($this->notification->text['charID']) ?? trans('web::seat.unknown');

                    $field->name('Character')
                        ->value($character->name)
                        ->long();
                });
            })
            ->warning();
    }
}