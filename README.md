API Documentation
Welcome to the API documentation for our Movie Application. This guide provides detailed information about the available endpoints, their usage, request parameters, and expected responses. This documentation is intended for frontend developers to integrate seamlessly with our backend services.

Table of Contents
Authentication
Login
Register
Logout
Get Current User
Movies
Get All Movies
Get Movie Details
Like a Movie
Favourite a Movie
Comments
Add a Comment
Authentication Details
Authentication
Login
Endpoint: /login

Method: POST

Description: Authenticates a user and returns an access token.

Request Body:

Parameter	Type	Description	Required
email	string	User's email address	Yes
password	string	User's password	Yes
Example Request:

json
Kopiraj kodo
POST /login
Content-Type: application/json

{
"email": "user@example.com",
"password": "securePassword123"
}
Successful Response:

Status Code: 200 OK

Body:

json
Kopiraj kodo
{
"token": "your_access_token_here"
}
Error Responses:

Invalid Credentials:
Status Code: 401 Unauthorized

Body:

json
Kopiraj kodo
{
"message": "Invalid credentials"
}
Register
Endpoint: /register

Method: POST

Description: Registers a new user and returns an access token.

Request Body:

Parameter	Type	Description	Required
email	string	User's email address	Yes
username	string	Desired username	Yes
password	string	User's password (min 8 characters)	Yes
password_confirmation	string	Confirmation of the password	Yes
Example Request:

json
Kopiraj kodo
POST /register
Content-Type: application/json

{
"email": "newuser@example.com",
"username": "newuser",
"password": "securePassword123",
"password_confirmation": "securePassword123"
}
Successful Response:

Status Code: 200 OK

Body:

json
Kopiraj kodo
{
"token": "your_access_token_here"
}
Error Responses:

Validation Errors:

Status Code: 422 Unprocessable Entity
Body: Contains validation error messages.
Invalid Credentials:

Status Code: 401 Unauthorized

Body:

json
Kopiraj kodo
{
"message": "Invalid credentials"
}
Logout
Endpoint: /logout

Method: POST

Description: Logs out the authenticated user by invalidating the access token.

Authentication: Required (Bearer Token)

Headers:

Header	Value
Authorization	Bearer your_token
Example Request:

http
Kopiraj kodo
POST /logout
Authorization: Bearer your_access_token_here
Successful Response:

Status Code: 200 OK
Body: Empty
Error Responses:

Unauthorized:
Status Code: 401 Unauthorized

Body:

json
Kopiraj kodo
{
"message": "Unauthorized"
}
Get Current User
Endpoint: /me

Method: POST

Description: Retrieves the authenticated user's details along with their liked and favourited movies.

Authentication: Required (Bearer Token)

Headers:

Header	Value
Authorization	Bearer your_token
Example Request:

http
Kopiraj kodo
POST /me
Authorization: Bearer your_access_token_here
Successful Response:

Status Code: 200 OK

Body:

json
Kopiraj kodo
{
"check": true,
"user": {
"id": 1,
"name": "test",
"email": "test@test.com"
},
"actions": {
"likes": [
{
"id": 1,
"name": "The Shawshank Redemption"
},
{
"id": 2,
"name": "The Godfather"
},
{
"id": 8,
"name": "The Lord of the Rings: The Return of the King"
}
],
"favourites": []
}
}
Error Responses:

Unauthorized:
Status Code: 401 Unauthorized

Body:

json
Kopiraj kodo
{
"check": false
}
Movies
Get All Movies
Endpoint: /movies

Method: GET

Description: Retrieves a list of movies. Supports optional filtering based on query parameters.

Authentication:

Public Access: Retrieves basic movie information.
Authenticated Users: Retrieves detailed movie information.
Query Parameters (Optional):

Parameter	Type	Description
q	string	Search query for movie titles or descriptions.
genres	array	List of genres to filter movies.
rating	float	Minimum rating to filter movies.
Example Requests:

Public Access:

http
Kopiraj kodo
GET /movies
With Filters:

http
Kopiraj kodo
GET /movies?q=action&genres[]=comedy&rating=4.5
Successful Response (Public):

Status Code: 200 OK

Body:

json
Kopiraj kodo
[
{
"id": 101,
"original_title": "Inception",
"release_year": 2010,
"genres": ["Action", "Sci-Fi"],
"rating": 4.8
},
{
"id": 102,
"original_title": "The Matrix",
"release_year": 1999,
"genres": ["Action", "Sci-Fi"],
"rating": 4.7
}
]
Successful Response (Authenticated):

Status Code: 200 OK
Body: Same as public response, potentially with additional details.
Error Responses:

Invalid Query Parameters:
Status Code: 400 Bad Request
Body: Contains error messages related to invalid filters.
Get Movie Details
Endpoint: /movie/{id}

Method: GET

Description: Retrieves detailed information about a specific movie by its ID.

Authentication:

Public Access: Basic movie details.
Authenticated Users: Additional details like user's like and favourite status.
URL Parameters:

Parameter	Type	Description	Required
id	int	ID of the movie	Yes
Example Request:

http
Kopiraj kodo
GET /movie/101
Successful Response (Public):

Status Code: 200 OK

Body:

json
Kopiraj kodo
{
"id": 101,
"original_title": "Inception",
"description": "A thief who steals corporate secrets...",
"release_year": 2010,
"genres": ["Action", "Sci-Fi"],
"rating": 4.8,
"director": "Christopher Nolan",
"cast": ["Leonardo DiCaprio", "Joseph Gordon-Levitt"]
}
Successful Response (Authenticated):

Status Code: 200 OK

Body: Includes additional fields indicating if the user has liked or favourited the movie.

json
Kopiraj kodo
{
"id": 101,
"original_title": "Inception",
"description": "A thief who steals corporate secrets...",
"release_year": 2010,
"genres": ["Action", "Sci-Fi"],
"rating": 4.8,
"director": "Christopher Nolan",
"cast": ["Leonardo DiCaprio", "Joseph Gordon-Levitt"],
"liked": true,
"favourited": false
}
Error Responses:

Movie Not Found:
Status Code: 404 Not Found

Body:

json
Kopiraj kodo
{
"message": "Movie not found"
}
Like a Movie
Endpoint: /movie/{id}/like

Method: POST

Description: Toggles the like status for a specific movie. If the movie is already liked by the user, it will be unliked, and vice versa.

Authentication: Required (Bearer Token)

URL Parameters:

Parameter	Type	Description	Required
id	int	ID of the movie	Yes
Headers:

Header	Value
Authorization	Bearer your_token
Example Request:

http
Kopiraj kodo
POST /movie/101/like
Authorization: Bearer your_access_token_here
Successful Response (Like Added):

Status Code: 200 OK

Body:

json
Kopiraj kodo
{
"message": "Like added"
}
Successful Response (Like Removed):

Status Code: 200 OK

Body:

json
Kopiraj kodo
{
"message": "Like removed"
}
Error Responses:

Unauthorized:

Status Code: 401 Unauthorized

Body:

json
Kopiraj kodo
{
"message": "Unauthorized"
}
Movie Not Found:

Status Code: 404 Not Found

Body:

json
Kopiraj kodo
{
"message": "Movie not found"
}
Favourite a Movie
Endpoint: /movie/{id}/favourite

Method: POST

Description: Toggles the favourite status for a specific movie. If the movie is already favourited by the user, it will be unfavourited, and vice versa.

Authentication: Required (Bearer Token)

URL Parameters:

Parameter	Type	Description	Required
id	int	ID of the movie	Yes
Headers:

Header	Value
Authorization	Bearer your_token
Example Request:

http
Kopiraj kodo
POST /movie/101/favourite
Authorization: Bearer your_access_token_here
Successful Response (Favourite Added):

Status Code: 200 OK

Body:

json
Kopiraj kodo
{
"message": "Favourite added"
}
Successful Response (Favourite Removed):

Status Code: 200 OK

Body:

json
Kopiraj kodo
{
"message": "Favourite removed"
}
Error Responses:

Unauthorized:

Status Code: 401 Unauthorized

Body:

json
Kopiraj kodo
{
"message": "Unauthorized"
}
Movie Not Found:

Status Code: 404 Not Found

Body:

json
Kopiraj kodo
{
"message": "Movie not found"
}
Comments
Add a Comment
Endpoint: /comment/add

Method: POST

Description: Adds a comment to a specific movie.

Authentication: Required (Bearer Token)

Headers:

Header	Value
Authorization	Bearer your_token
Request Body:

Parameter	Type	Description	Required
comment	string	The comment text	Yes
movie_id	int	ID of the movie to comment on	Yes
Example Request:

json
Kopiraj kodo
POST /comment/add
Content-Type: application/json
Authorization: Bearer your_access_token_here

{
"comment": "Amazing movie with stunning visuals!",
"movie_id": 101
}
Successful Response:

Status Code: 200 OK

Body:

json
Kopiraj kodo
{
"id": 201,
"comment": "Amazing movie with stunning visuals!",
"movie_id": 101,
"user_id": 1,
"created_at": "2024-12-20T10:00:00Z",
"updated_at": "2024-12-20T10:00:00Z"
}
Error Responses:

Validation Errors:

Status Code: 422 Unprocessable Entity
Body: Contains validation error messages.
Unauthorized:

Status Code: 401 Unauthorized

Body:

json
Kopiraj kodo
{
"message": "Unauthorized"
}
Movie Not Found:

Status Code: 404 Not Found

Body:

json
Kopiraj kodo
{
"message": "Movie not found"
}
Authentication Details
Token-Based Authentication
Our API uses token-based authentication to secure endpoints that require user authorization. To access protected routes, clients must include a valid Bearer token in the Authorization header of the HTTP request.

Obtaining a Token:

Login: Send a POST request to /login with valid credentials to receive an access token.
Register: Send a POST request to /register with user details to create a new account and receive an access token.
Using the Token:

Include the token in the Authorization header for all protected endpoints:

http
Kopiraj kodo
Authorization: Bearer your_access_token_here
Token Renewal:

Tokens are issued upon successful authentication and should be stored securely on the client side. Implement token renewal strategies as needed based on token expiration policies.

Logging Out:

To invalidate a token, send a POST request to /logout with the token included in the Authorization header.

Notes
All date-time values are in ISO 8601 format (YYYY-MM-DDTHH:MM:SSZ).
Ensure that all request bodies are sent in JSON format with the appropriate Content-Type header.
Handle error responses gracefully by providing user-friendly messages based on the error codes and messages received.
For any further assistance or queries, please refer to the developer support channels.
Thank you for using our API! We hope this documentation helps you integrate smoothly with our services. Happy coding!
