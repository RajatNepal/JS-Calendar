# CSE330

Rajat Nepal / 490732 / RajatNepal

Anton Dmitriev / 485865 / AntonDmitriev1484



Link to calendar website: EC2 instance is shut down. Email me to have it restarted to use website.


Login Details (both links have these users):
  - You can create your own account
  - If you want to access pre-existing accounts you can use
    - u: test p: test
    - u: rajat p: nepal
  
- Creative Portion: User groups.
  - Users see a list of all groups on the calendar site
  - Users can join groups
  - Users can create groups, when they do, they become that group's admin and can:
    - Edit information about the group
    - Create events for that group
    - Edit events for that group
  - Group events appear in each group member's calendar. When they get updated by the admin, they are also updated for each member.

- Creative Portion: Event tags.
  - Each event can have a tag associated with it.








Planning (Ignore):

  - Modifying Event display system
    - ~~Enable minimal event display within calendar divs~~
    - ~~Modify disEventsList to generally be hidden and otherwise only display events for some specific day on click~~

    - Fix:
        - ~~Displaying events for the current month we're on. Currently displaying events for hard coded month "3"~~
        - Returning to login page on refresh
        - ~~PHP not properly terminating session~~
        - ~~Session cookie is httponly~~
        - ~~Bug with group events being added twice to one account instead of being evenly distributed between both~~
        - ~~Event tags not working on event creation but working on event edit~~

    - ~~Create group menu~~
    - ~~Delete group option~~
    - ~~Edit group menu ~~
      - ~~Create group events (group admin has permission, regular user does not)~~
      - ~~Edit group events (group admin has permission, regular user does not)~~


    - ~~Join groups~~
    - ~~Leave groups~~
    - ~~Display owned groups~~
    - ~~Displaying group events (along with already existing events) on user calendars (it already does this by default I think lol)~~

    
    - ~~General security check~~
    - ~~General citation check~~

- ~~Set up MySQL DB~~

- Server + Client Connection
  - ~~create_account~~
  - ~~login~~
  - ~~logout~~

  - ~~create_event~~
  - ~~edit_event~~
  - ~~delete_event~~

  - ~~user_events~~
    - ~~over full month~~
    - ~~over range~~

  - ~~switch token to document.cookies~~
  - ~~set up proper function output~~
  - ~~set up import/export components from requests.js~~


  - [Creative Portion]
    - Groups (5 points) -> With this plan (10 points)
      - ~~Create users_groups junction table in MySQL (keep track of all users in each group)~~

      - ~~create_group~~
      - ~~edit_group~~
      - ~~delete_group~~
      - ~~Modify groups table to include group_event_id~~
      - ~~Create junction table for group_event_id~~
      - ~~create_group_event (update)~~
      - ~~edit_group_event~~
      - ~~edit create_event() edit_event()~~
      - ~~group_events (should be similar to user_events but searches by group instead of by user)~~

      - ~~get_group_members~~
      - ~~get_group_owner~~

      - ~~add_user_to_group~~
      - ~~remove_user_from_group~~

    - Event Tags (5 points) (easier than groups)
      - ~~Add tags table to database~~
      - ~~Modify groups table to include foreign key to tags~~
      - ~~Update all event connection methods to incorporate tags~~
        - ~~To change an event's tag you'd just call the corresponding edit event.~~
      - ~~get_tags~~
      - ~~create_tags (Not necessary, just manually add tags to database, don't give users options to make new ones)~~

      - + ~~Frontend~~


      

  




