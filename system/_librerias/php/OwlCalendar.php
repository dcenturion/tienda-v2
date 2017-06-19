<?php
session_start();

include_once "funciones.php";
include_once "Google/src/Google/autoload.php";

class Calendar{
    public $events;

    public function __construct() {
        $this->events = new CalendarResource();
    }
}

class Event{
    const NOTIFY_STATUS_PENDING    = "pending";
    const NOTIFY_STATUS_CONFIRMED  = "confirmed";

    private $eventId;
    private $title;
    private $description;
    private $type;
    private $location;
    private $startHour;
    private $finalHour;
    private $color;
    private $status;
    private $dateCreate;
    private $userId;
    private $googleCalendarEventId;
    private $notifyStatus;
    private $parentEventId;
    private $calendarId;
    //other
    private $eventMeta;
    
    private $keys;
    
    public function __construct(array $eventData = null) {
        //default values
        $this->type         = "event";
        $this->startHour    = date("H:i:s");
        $this->finalHour    = date("H:i:s");
        $this->color        = "blue";
        $this->status       = "process";
        $this->dateCreate   = FechaHoraSrv();
        $this->notifyStatus = "pending";
        
        $this->keys = [
            "eventId",
            "title",
            "description",
            "type",
            "location",
            "startHour",
            "finalHour",
            "color",
            "status",
            "dateCreate",
            "userId",
            "googleCalendarEventId",
            "notifyStatus",
            "parentEventId",
            "calendarId",
            
            "eventMeta"
        ];
        
        if($eventData){
            foreach ($eventData as $key => $value){
                if(in_array($key, $this->keys)){
                    if($key === "eventMeta"){
                        if(!is_a($value, "EventMeta")){
                            throw new Exception("eventMeta param isn't a EvenMeta object");
                        }
                    }
                        
                    $this->{$key} = $value;
                }
            }
        }
    }
    
    function getEventId() {
        return $this->eventId;
    }

    function getTitle() {
        return $this->title;
    }

    function getDescription() {
        return $this->description;
    }

    function getType() {
        return $this->type;
    }

    function getLocation() {
        return $this->location;
    }

    function getStartHour() {
        return $this->startHour;
    }

    function getFinalHour() {
        return $this->finalHour;
    }

    function getColor() {
        return $this->color;
    }

    function getStatus() {
        return $this->status;
    }

    function getDateCreate() {
        return $this->dateCreate;
    }

    function getUserId() {
        return $this->userId;
    }

    function getGoogleCalendarEventId() {
        return $this->googleCalendarEventId;
    }

    function getNotifyStatus() {
        return $this->notifyStatus;
    }

    function getParentEventId() {
        return $this->parentEventId;
    }
    
    function getCalendarId() {
        return $this->calendarId;
    }

    public function getEventMeta(){
        if($this->eventMeta){
            return $this->eventMeta;
        }else{
            if(!$this->eventId){
                throw new Exception("Event don't have eventId");
            }else{
                $eventMetaData = fetchOne("
                SELECT 
                Codigo as eventMetaId,
                event_id as eventId,
                date_start as dateStart,
                interval_case as intervalCase,
                interval_value as intervalValue,
                week,
                week_day as weekDay
                FROM event_meta
                WHERE event_id = {$this->eventId}");

                if($eventMetaData){
                    $this->setEventMeta(new EventMeta((array) $eventMetaData));
                    
                    return $this->getEventMeta();
                }else{
                    throw new Exception("Error when fetch eventMeta of event, record in database no exists");
                }
            }
        }
    }
    
    function setEventId($eventId) {
        $this->eventId = $eventId;
    }

    function setTitle($title) {
        $this->title = $title;
    }

    function setDescription($description) {
        $this->description = $description;
    }

    function setType($type) {
        $this->type = $type;
    }

    function setLocation($location) {
        $this->location = $location;
    }

    function setStartHour($startHour) {
        $this->startHour = $startHour;
    }

    function setFinalHour($finalHour) {
        $this->finalHour = $finalHour;
    }

    function setColor($color) {
        $this->color = $color;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setDateCreate($dateCreate) {
        $this->dateCreate = $dateCreate;
    }

    function setUserId($userId) {
        $this->userId = $userId;
    }

    function setGoogleCalendarEventId($googleCalendarEventId) {
        $this->googleCalendarEventId = $googleCalendarEventId;
    }

    function setNotifyStatus($notifyStatus) {
        $this->notifyStatus = $notifyStatus;
    }

    function setParentEventId($parentEventId) {
        $this->parentEventId = $parentEventId;
    }
    
    function setCalendarId($calendarId) {
        $this->calendarId = $calendarId;
    }

    function setEventMeta(EventMeta $eventMeta) {
        $this->eventMeta = $eventMeta;
    }
    
    //other functions
    public function childEventsId(){
        if(!$this->getEventId()){
            throw new Exception("Event don't have eventId");
        }else{
            //children events
            return fetchMx("
            SELECT Codigo 
            FROM event
            WHERE parent_event_id = {$this->getEventId()}", "Codigo");
        }
    }
    
    public function getGoogleCalendarEvent(){
        if($this->getGoogleCalendarEventId()){
            //verify google access
            $myGoogleClient = get_my_google_client($this->getUserId());

            if($myGoogleClient){
                $client = $myGoogleClient->client;

                $service = new Google_Service_Calendar($client);
                
                return $service->events->get('primary', $this->getGoogleCalendarEventId());
            }
        }else{
            return null;
        }
    }
}

class EventMeta{
    private $eventMetaId;
    private $eventId;
    private $dateStart;
    private $intervalCase;
    private $intervalValue;
    private $week;
    private $weekDay;
    
    
    private $keys;
    
    public function __construct(array $eventMetaData = null) {
        //default values
        $this->dateStart        = date('Y-m-d');
        $this->intervalCase     = "once";
        $this->intervalValue    = 0;
        
        $this->keys = [
            "eventMetaId",
            "eventId",
            "dateStart",
            "intervalCase",
            "intervalValue",
            "week",
            "weekDay"
        ];
        
        if($eventMetaData){
            foreach ($eventMetaData as $key => $value){
                if(in_array($key, $this->keys)){
                    $this->{$key} = $value;
                }
            }
        }
    }
    
    function getEventMetaId() {
        return $this->eventMetaId;
    }

    function getEventId() {
        return $this->eventId;
    }

    function getDateStart() {
        return $this->dateStart;
    }

    function getIntervalCase() {
        return $this->intervalCase;
    }

    function getIntervalValue() {
        return $this->intervalValue;
    }

    function getWeek() {
        return $this->week;
    }

    function getWeekDay() {
        return $this->weekDay;
    }

    function setEventMetaId($eventMetaId) {
        $this->eventMetaId = $eventMetaId;
    }

    function setEventId($eventId) {
        $this->eventId = $eventId;
    }

    function setDateStart($dateStart) {
        $this->dateStart = $dateStart;
    }

    function setIntervalCase($intervalCase) {
        $this->intervalCase = $intervalCase;
    }

    function setIntervalValue($intervalValue) {
        $this->intervalValue = $intervalValue;
    }

    function setWeek($week) {
        $this->week = $week;
    }

    function setWeekDay($weekDay) {
        $this->weekDay = $weekDay;
    }
}

//Handlers
class CalendarResource{
    public function __construct() {
        
    }
    
    public function listEvents(array $optionParams = null){
        $events = [];
        $JOIN_REFERENCE = "";
        $FIRST_PART_CONDITIONALS = [];
        $SECOND_PART_CONDITIONALS = "";
        
        if($optionParams){
            foreach ($optionParams as $key => $value){
                switch ($key){
                    case "status":
                    case "user_id":
                    case "notify_status":
                    case "parent_event_id":
                        if(is_array($value)){
                            $value = array_map(function($element){
                                return "'{$element}'";
                            }, $value);
                            
                            $value = implode(",", $value);
                            
                            $FIRST_PART_CONDITIONALS[]= "{$key} IN ({$value})";
                        }else{
                            $FIRST_PART_CONDITIONALS[]= "{$key} = '{$value}'";
                        }
                        break;
                    case "last_event":
                        $SECOND_PART_CONDITIONALS = "LIMIT 0,1";
                        break;
                    case "calendar_id":
                        $JOIN_REFERENCE = "INNER JOIN calendar CA ON E.calendar_id = CA.Codigo";
                        $FIRST_PART_CONDITIONALS[]= "CA.Codigo = '{$value}'" ;
                        break;
                    case "maxResults":
                        $value = (int) $value;
                        
                        if($value){
                            $SECOND_PART_CONDITIONALS = "LIMIT 0,{$value}";
                        }
                        break;
                }
            }
        }
        
        $FIRST_PART_CONDITIONALS = ($FIRST_PART_CONDITIONALS)? "WHERE " . implode(" AND ", $FIRST_PART_CONDITIONALS) : null;
        
        $eventsSQL = "SELECT 
        E.Codigo as eventId,
        E.title,
        E.description,
        E.type,
        E.location,
        E.start_hour,
        E.final_hour,
        E.color,
        E.status,
        E.date_create,
        E.user_id,
        E.google_calendar_event_id,
        E.notify_status,
        E.parent_event_id,
        E.calendar_id,
        EM.Codigo as eventMetaId,
        EM.event_id,   
        EM.date_start,   
        EM.interval_case,   
        EM.interval_value,   
        EM.week,   
        EM.week_day
        FROM event E
        INNER JOIN event_meta EM ON EM.event_id = E.Codigo
        {$JOIN_REFERENCE}
        {$FIRST_PART_CONDITIONALS}
        ORDER BY E.Codigo DESC
        {$SECOND_PART_CONDITIONALS}";
        
        $eventsData = fetchAll($eventsSQL);
        
        foreach ($eventsData as $eventData){
            $events[]= new Event([
                "eventId"       => $eventData->eventId,
                "title"         => $eventData->title,
                "description"   => $eventData->description,
                "type"          => $eventData->type,
                "location"      => $eventData->location,
                "startHour"     => $eventData->start_hour,
                "finalHour"     => $eventData->final_hour,
                "color"         => $eventData->color,
                "status"        => $eventData->status,
                "dateCreate"    => $eventData->date_create,
                "userId"        => $eventData->user_id,
                "googleCalendarEventId" => $eventData->google_calendar_event_id,
                "notifyStatus"          => $eventData->notify_status,
                "parentEventId"         => $eventData->parent_event_id,
                "calendarId"         => $eventData->calendar_id,
                
                "eventMeta"     => new EventMeta([
                    "eventMetaId"   => $eventData->eventMetaId,
                    "eventId"       => $eventData->event_id,
                    "dateStart"     => $eventData->date_start,
                    "intervalCase"  => $eventData->interval_case,
                    "intervalValue" => $eventData->interval_value,
                    "week"          => $eventData->week,
                    "weekDay"       => $eventData->week_day
                ])
            ]);
        }
        
        return $events;
    }
    
    public function get($eventId){
        $eventData = fetchOne("
        SELECT 
        Codigo as eventId,
        title,
        description,
        type,
        location,
        start_hour as startHour,
        final_hour as finalHour,
        color,
        status,
        date_create as dateCreate,
        user_id as userId,
        google_calendar_event_id as googleCalendarEventId,
        notify_status as notifyStatus,
        parent_event_id as parentEventId,
        calendar_id as calendarId
        FROM event
        WHERE Codigo = {$eventId}");
        
        if($eventData){
            return new Event((array) $eventData);
        }else{
            throw new Exception("Error when fetch event, record in database no exists");
        }
    }
    
    public function insert(Event $event, array $optionParams = null){
        try{
            $event->getEventMeta();
        }catch (Exception $e){
            throw new Exception("Don't exists eventMeta data");
        }
        
        $attendees = null;
        
        //option params filter
        if($optionParams){
            //attendees
            $attendeesParam = $optionParams["attendees"];
            
            if($attendeesParam && is_array($attendeesParam)){
                $attendees = [];
                
                foreach($attendeesParam as $attendee){
                    if(is_string($attendee) && filter_var($attendee, FILTER_VALIDATE_EMAIL)){
                        if($attendee !== $event->getUserId()){
                            $attendees[]= ["email" => $attendee];
                        }
                    }else{
                        throw new Exception("attendees values should be a string whit valid email value");
                    }
                }
            }
        }
        
        
        $eventMeta = $event->getEventMeta();
        
        //verify google access
        $myGoogleClient = get_my_google_client($event->getUserId());
        
        if($myGoogleClient){
            //if exists Google access exits when record a GoogleCalendarEvent
            $dateStart = new DateTime($eventMeta->getDateStart());
            $dateStart->add(new DateInterval("P{$eventMeta->getIntervalValue()}D"));
            $dateEnd = $dateStart->format("Y-m-d");

            $googleCalendarEvent = new Google_Service_Calendar_Event(array(
                "summary"       => $event->getTitle(),
                "location"      => $event->getLocation(),
                "description"   => $event->getDescription(),
                "start" => array(
                    "dateTime"  => "{$eventMeta->getDateStart()}T{$event->getStartHour()}",
                    "timeZone"  => "America/Lima",
                ),
                "end"   => array(
                    "dateTime"  => "{$dateEnd}T{$event->getFinalHour()}",
                    "timeZone"  => "America/Lima",
                ),
                "recurrence"    => array(
                    "RRULE:FREQ=DAILY;COUNT=1"
                ),
                "attendees"     => ($attendees)? $attendees : null,
                "reminders"     => array(
                    "useDefault"    => FALSE,
                    "overrides"     => array(
                        array("method" => "email", "minutes" => 24 * 60),
                        array("method" => "popup", "minutes" => 10),
                    ),
                ),
            ));
            
            $client = $myGoogleClient->client;
            
            $service = new Google_Service_Calendar($client);        
            
            $calendarId = 'primary';
            
            $googleCalendarEventInserted = $service->events->insert($calendarId, $googleCalendarEvent, [
                "sendNotifications" => true
            ]);

            $event->setGoogleCalendarEventId($googleCalendarEventInserted->id);
        }
        
        //record the event
        $eventDataToInsert = [
            "title"         => $event->getTitle(),
            "description"   => $event->getDescription(),
            "type"          => $event->getType(),
            "location"      => $event->getLocation(),
            "start_hour"    => $event->getStartHour(),
            "final_hour"    => $event->getFinalHour(),
            "color"         => $event->getColor(),
            "status"        => $event->getStatus(),
            "date_create"   => $event->getDateCreate(),
            "user_id"       => $event->getUserId(),
            "google_calendar_event_id"  => $event->getGoogleCalendarEventId(),
            "notify_status"             => $event->getNotifyStatus(),
            "parent_event_id"           => $event->getParentEventId(),
            "calendar_id"               => $event->getCalendarId(),
        ];
        
        $eventDataInserted = insert("event", $eventDataToInsert);
        $event->setEventId($eventDataInserted["lastInsertId"]);
        
        //record the eventMeta
        $eventMeta->setEventId($event->getEventId());
        
        $eventMetaDataToInsert = [
            "event_id"          => $eventMeta->getEventId(),
            "date_start"        => $eventMeta->getDateStart(),
            "interval_case"     => $eventMeta->getIntervalCase(),
            "interval_value"    => $eventMeta->getIntervalValue(),
            "week"              => $eventMeta->getWeek(),
            "week_day"          => $eventMeta->getWeekDay()
        ];
        
        $eventMetaDataInserted = insert("event_meta", $eventMetaDataToInsert);
        $eventMeta->setEventMetaId($eventMetaDataInserted["lastInsertId"]);
        
        //verify attendees
        if($attendees){
            $eventDataToInsert["google_calendar_event_id"] = null;
            $eventDataToInsert["notify_status"] = "pending";
            $eventDataToInsert["parent_event_id"] = $event->getEventId();
            
            foreach ($attendees as $attendee){
                $eventDataToInsert["user_id"] = $attendee["email"];
                
                $eventDataInsertedOfAttendee = insert("event", $eventDataToInsert);
                
                $eventMetaDataToInsert["event_id"] = $eventDataInsertedOfAttendee["lastInsertId"];
                        
                insert("event_meta", $eventMetaDataToInsert);
            }
        }
        
        return $event;
    }
    
    public function update(Event $event){
        global $PDO;
        
        if(!$event->getEventId()){
            throw new Exception("Event don't have eventId when event don't exists");
        }else{
            update("event", [
                "title"         => $event->getTitle(),
                "description"   => $event->getDescription(),
                "type"          => $event->getType(),
                "location"      => $event->getLocation(),
                "start_hour"    => $event->getStartHour(),
                "final_hour"    => $event->getFinalHour(),
                "color"         => $event->getColor(),
                "status"        => $event->getStatus(),
                "date_create"   => $event->getDateCreate(),
                "user_id"       => $event->getUserId(),
                "google_calendar_event_id"  => $event->getGoogleCalendarEventId(),
                "notify_status"             => $event->getNotifyStatus(),
                "parent_event_id"           => $event->getParentEventId(),
                "calendar_id"               => $event->getCalendarId(),
            ],[
                "Codigo"                    => $event->getEventId()
            ]);
            
            //Event::eventId exits when not problem
            $eventMeta = $event->getEventMeta();
            
            update("event_meta", [
                "event_id"          => $eventMeta->getEventId(),
                "date_start"        => $eventMeta->getDateStart(),
                "interval_case"     => $eventMeta->getIntervalCase(),
                "interval_value"    => $eventMeta->getIntervalValue(),
                "week"              => $eventMeta->getWeek(),
                "week_day"          => $eventMeta->getWeekDay()
            ],[
                "Codigo"            => $eventMeta->getEventMetaId()
            ]);
            
            //verify if exists attendees
            $childEventsId = $event->childEventsId();
            
            if($childEventsId){
                $filterOfChildEventsId = implode(",", $childEventsId);
                
                $rowsAffected = $PDO->exec("
                UPDATE event 
                SET title = '{$event->getTitle()}',
                description = '{$event->getDescription()}',
                type = '{$event->getType()}',
                location = '{$event->getLocation()}',
                start_hour = '{$event->getStartHour()}',
                final_hour = '{$event->getFinalHour()}',
                color = '{$event->getColor()}'
                WHERE Codigo IN ({$filterOfChildEventsId})");
                
                $rowsAffected = $PDO->exec("
                UPDATE event_meta 
                SET date_start = '{$eventMeta->getDateStart()}',
                interval_case = '{$eventMeta->getIntervalCase()}',
                interval_value = '{$eventMeta->getIntervalValue()}',
                week = '{$eventMeta->getWeek()}',
                week_day = '{$eventMeta->getWeekDay()}'
                WHERE event_id IN ({$filterOfChildEventsId})");
            }

            if($event->getGoogleCalendarEventId()){
                //verify google access
                $myGoogleClient = get_my_google_client($event->getUserId());
                
                if($myGoogleClient){
                    $client = $myGoogleClient->client;
                    
                    $service = new Google_Service_Calendar($client);

                    $googleEvent = $service->events->get('primary', $event->getGoogleCalendarEventId());
                    $googleEvent->setSummary($event->getTitle());
                    $googleEvent->setDescription($event->getDescription());
                    $googleEvent->setLocation($event->getLocation());

                    ////////////////////////////////////////////////////////////////////////
                    //create google calendar datetime
                    $dateStart = $eventMeta->getDateStart();
                    $dateEnd = new DateTime($eventMeta->getDateStart());
                    $startHour = $event->getStartHour();
                    $finalHour = $event->getFinalHour();

                    //dateEnd
                    $intervalValue = $eventMeta->getIntervalValue();
                    $dateEnd->add(new DateInterval("P{$intervalValue}D"));
                    $dateEnd = $dateEnd->format("Y-m-d");

                    $start_event_date_time = new Google_Service_Calendar_EventDateTime();
                    $start_event_date_time->setDateTime("{$dateStart}T{$startHour}");
                    $start_event_date_time->setTimeZone("America/Lima");

                    $end_event_date_time = new Google_Service_Calendar_EventDateTime();
                    $end_event_date_time->setDateTime("{$dateEnd}T{$finalHour}");
                    $end_event_date_time->setTimeZone("America/Lima");
                    ////////////////////////////////////////////////////////////////////////

                    $googleEvent->setStart($start_event_date_time);
                    $googleEvent->setEnd($end_event_date_time);

                    $service->events->update('primary', $googleEvent->getId(), $googleEvent);
                }
            }
        }
        
        return $event;
    }
    
    public function delete($eventId){
        global $PDO;
        
        $event = $this->get((int) $eventId);
        
        //Delete evento from event
        delete("event", [
            "Codigo"    => $event->getEventId()
        ]);

        //Delete evento from event_meta
        delete("event_meta", [
            "event_id"  => $event->getEventId()
        ]);

        //verify if exists attendees
        $childEventsId = $event->childEventsId();

        if($childEventsId){
            $filterOfChildEventsId = implode(",", $childEventsId);

            $rowsAffected = $PDO->exec("
            DELETE
            FROM event
            WHERE Codigo IN ({$filterOfChildEventsId})");

            $rowsAffected = $PDO->exec("
            DELETE
            FROM event_meta
            WHERE event_id IN ({$filterOfChildEventsId})");
        }
        
        if($event->getGoogleCalendarEventId()){
            //verify google access
            $myGoogleClient = get_my_google_client($event->getUserId());

            if($myGoogleClient){
                $client = $myGoogleClient->client;

                $service = new Google_Service_Calendar($client);
                
                $service->events->delete('primary', $event->getGoogleCalendarEventId());
            }
        }
    }
}