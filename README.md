# wemod-sample
Sample code for Wemod take home challenge.
## Installation
1. Clone repo `https://github.com/stephen-toth-42/wemod-sample.git`
2. `cd wemod-sample`
3. Build Docker image `docker build -t sample-test:latest .`
4. Run Docker container `docker run -d -p 8080:80 -v ./src/data sample-test:latest`

### Sample Data
Copy the following to a .csv file and either an API platform (Postman, Restfox) or CURL to upload the file to the API.
```
user,long_url
company1,https://www.philadelphiaeagles.com/news/eagles-playoff-scenarios-road-to-victory-2024
company2,https://disneyworld.disney.go.com/resorts/bay-lake-tower-at-contemporary/rates-rooms/
```

## Solution
The approach focuses on making a backend API solution to the problem.
The approach focuses on making a backend API solution to the problem deployed within a Docker container.

### Endpoints
#### POST /api/v1/upload
Allows users to upload CSV files as files.
##### Response
An array of records including the short urls created.
```
[
    {
        "user": "steve",
        "long_url": "https://www.philadelphiaeagles.com/news/eagles-playoff-scenarios-road-to-victory-2024",
        "short_url": "http://localhost/page/1",
        "action": "created"
    },
    {
        "user": "bob",
        "long_url": "https://disneyworld.disney.go.com/resorts/bay-lake-tower-at-contemporary/rates-rooms/",
        "short_url": "http://localhost/page/2",
        "action": "created"
    }
]
```
#### GET /page/{link_id}
Represents the short URLs generated by the system.  It is assumed that these will be accessed via a web browser and as such does not use the `api` prefix.  The use of the word `page` within the URL could further be shortened, but has been left as-is for clarity in the solution.
#### Response
The user is forwarded to the long URL address.

#### GET /api/v1/analytics
Returns JSON representing the URLs and the number of hits.
##### Query Parameters
- user : A string representing the user to filter to for the response.
##### Response
```
{
    "steve": [
        {
            "short_url": "http://localhost/page/1",
            "long_url": "https://www.philadelphiaeagles.com/news/eagles-playoff-scenarios-road-to-victory-2024",
            "hits": 2
        }
    ],
    "bob": [
        {
            "short_url": "http://localhost/page/2",
            "long_url": "https://disneyworld.disney.go.com/resorts/bay-lake-tower-at-contemporary/rates-rooms/",
            "hits": 0
        }
    ]
}
```

---

## Backend Take Home Challenge
Let us pretend we are a brand new startup with a simple mission, providing shortened URLs for enterprise companies. Below you will find our product requirements and technical specifications. Do your best to meet all requirements and document any assumptions made. This is the main code assessment in the WeMod interview process, so be sure to be proud of both the functionality and design of the code you submit.

### Product Requirements
- Users should be able to upload a batch of Long URLs to be shortened via a CSV file.
- Short URLs should redirect the user to the Long URL destination.
- Each visit to a Short URL should be tracked for analytics.
- An endpoint to retrieve data about the Short URL, like the visit analytics.
### Technical Specifications
- Provide documentation that explains how to run the provided code and access the service in a local development environment.
- Code should be clean, concise, and self-documenting.
- PHP should be used; version 8.2 or greater.
- The latest version of either Laravel or Symfony should be utilized.
- Use the framework and language features to write well engineered code
- Data should be persisted in a relational database.
- Proper authentication is not required.

### Base Assumptions
- Another team of frontend developers would create the UX for the system using the API
- Code is streamlined and focused only on CSV to minimize development time given requirements
- The requirement to use a relational database indicates a reasonable volume for the system that can be handled via horizontal scaling or partitioning
- Indexing on the long URLs were added to avoid duplication for single users
### Considerations
- Move the CSV handling to a driver so that alternatives could be used
- Use authentication to remove the need to specify users within the CSV

### Questions for Requirements
- How exactly should redirection be handled?
  - Assumptions: The visitors are using a web browser with the intent of being taken to the long URL by using the short URL.  The standard PHP `redirect()` function has been used to accomplish this cleanly and quickly.
- What is the expected load or volume?
  - Assumptions: CSV uploading is a weekly or monthly occurrance for users.  The use of MySQL is sufficient for volume of ingestion which can be batched without immediate availability.  The use of the primary key of the `links` table as the short URL ensures quick reads during redirect.
  - Considerations: If the volume is expected to be higher, partitioning DB servers by user would increase availability, and a load-balancing auto-scaler for the PHP servers would allow for better response times.  If volume is significantly high, it may be advantageous to move the analytics to a NoSQL solution to accomodate faster writes with or without user-based partitioning.  It would also be possible to run ingestion of the CSV files during off hours or as lower priority processes.
- How many users and how many URLs per user are expected?
  - Assumptions: Similar to above, the expectations are assumed to be within the capabilities of the systems listed in teh technical specification.
- What is the nature of use for the short URLs?
  - Assumptions: Given the nature of the problem, it is assumed that this would be used for marketing and analytics, or click tracking.  Depending on the target of the effort, this could significantly influence decisions on scalability above.
- How large the CSV files expected to be?
  - Assumption: Files are expected to be under standard single file upload limits focused on new URLs.
  - Considerations: If larger file sizes are needed then the upload could be chuncked with the ability to restart if the connection is broken.  This could be accomplished using pre-signed URLs to directly upload to blob storage such as S3