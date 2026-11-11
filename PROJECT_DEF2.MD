I need the following updates:

1.- Email address will be used only for log in pupose. Users registered in the social network should not see the email of other users. Instead, add a new field named userid to the user profile.

2.- The userid field should be a text type with 15 chars of length. This field should required during the registration and same as email, this should unique. That means, when the new registration tries to create the profile it will check the the userid is not already in the system, if so, it should show an error message.

3.- need to add another field named "physical_description” to the user profile. This field should be a text area with 500 chars of length. No need required during registration. It could be added in the profile update

4- need to add another field named "finding_preference” to the user profile. This field should be a text area with 500 chars of length. No need required during registration. It could be added in the profile update

5.- At the profile page, the user needs to have the ability to delete the pictures of thier own.

6.- I need to separate the "Password Change" and "Pictures" process in separate routes and add an option to the left menu for each route.

7.- I need a new route named "Public_Profile" which show the user pictures, and the basic information: - profile picture - userid - name - gender - city, state and country - physical_description - finding_preference
Set this as another option in the left menu, so each user can see thier own public profile

8.- In the "pictures" route, move the filter option to a pop-up window and set an option in the left menu named "Search Profiles". But instead of filtering new pictures, add another list of profile pictures that show: - profile picture - userid - city, state, country

9.- The new "Pictures" list should not filtered any more. But I need another option in the left menu that says "New Pictures" for this route.

10.- Create a public profile counter and every time a user visit another user profile, increments the counter and maintain a log that register, date, time and userid if who visted the profile.

11.- Create another ooption in the left menu that says "Who visted me". And in that route, show the visited log showing date, time and userid of who visited my public profile
