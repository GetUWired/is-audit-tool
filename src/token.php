<?php

namespace ISAudit;

use \PDO;

/**
 *
 */
class Token
{



    /**
     * This function will update the tokens in the database if they have expired and need to be refreshed
     *
     * @param $accessToken
     */
    function update_tokens_in_database($accessToken)
    {
        if ($accessToken) {
            //Create connection to the database
            $db = new PDO('sqlite:oauth-is.db');

            $token = serialize($accessToken);

            $stmt = $db->prepare("UPDATE oauth SET access_token = ? WHERE id = 1");

            $stmt->bindValue(1, $token, PDO::PARAM_LOB);

            $result = $stmt->execute();

            // //Close the stmt
            // $stmt->close();

            // //Close the MySQL Connection
            // $db->close();
        }

        //Return the result to the script
        return;
    }

    /**
     * Retrieve the access token from the database to use in following API calls
     *
     */
    function retrieve_tokens_in_database()
    {
        //Create connection to the database
        // $db = new SQLite3('/oauth-is.db');
        $db = new PDO('sqlite:../oauth-is.db');

        //Select the tokens from the database and grab the newest by timestamp
        $stmt = $db->prepare("SELECT access_token FROM oauth WHERE id = :id");

        $id = 1;
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);

        //Execute the prepared statement
        $result = $stmt->execute();

        $tmpArr = $result->fetchArray(SQLITE3_NUM);

        // //Close the stmt
        // $stmt->close();

        // //Close the MySQL Connection
        // $db->close();

        //Return the result to the script
        return $tmpArr;
    }
}
