<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NotificationEvent
 *
 * @author Jona
 */


namespace P4M\NotificationBundle\Event;

final class NotificationEvents
{
    const onNotificationLike = 'p4m.notification.like';
    const onNotificationComment = 'p4m.notification.comment';
}
