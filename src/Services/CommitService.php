<?php
namespace TeamNeusta\PushMe\Services;


use TeamNeusta\PushMe\DTO\Author;
use TeamNeusta\PushMe\DTO\Commit;

class CommitService
{

    const COMMIT_HASH_PATTERN = "/\[hash\=(.*)\]\[branch/";
    const BRANCH_PATTERN = "/\[branch\=(.*)\]\[message/";
    const MESSAGE_PATTERN = "/\[message\=(.*)\]\[committed/";
    const COMMIT_DATE_PATTERN = "/\[committed\=(.*)\]\[author_name/";

    const AUTHOR_NAME_PATTERN = "/\[author_name\=(.*)\]\[author_mail/";
    const AUTHOR_EMAIL_PATTERN = "/\[author_mail\=(.*)\]$/";

    const MERGE_COMMIT_PATTERN = "/\[message\=Merge branch (.*)\]\[committed/";

    public function prepareMessages(array $commits = [])
    {
        $commitData = [];
        foreach ($commits as $commit){
            //skip merge commits
            //if(preg_match(self::MERGE_COMMIT_PATTERN, $commit)){
            //    continue;
            //}

            $commitObject = new Commit();
            // get commit Hash
            $commitHashFound = preg_match(self::COMMIT_HASH_PATTERN, $commit, $commitHash);
            if((bool)$commitHashFound){
                $commitHash = $commitHash[1];
                $commitObject->setCommitHash($commitHash);
            }
            // get branch if exist
            $branchFound = preg_match(self::BRANCH_PATTERN, $commit, $branch);
            if((bool)$branchFound) {
                $branch = $branch[1];
                $commitObject->setBranch($branch);
            }
            // get message if exist
            $messageFound = preg_match(self::MESSAGE_PATTERN, $commit, $message);
            if((bool)$messageFound) {
                $message = $message[1];
                $commitObject->setMessage($message);
            }
            // get committed date if exist
            $dateFound = preg_match(self::COMMIT_DATE_PATTERN, $commit, $date);
            if((bool)$dateFound) {
                $date = $date[1];
                $commitObject->setCommitDate($date);
            }
            // preparing author
            $author = new Author();
            // get author name if exist
            $nameFound = preg_match(self::AUTHOR_NAME_PATTERN, $commit, $name);
            if((bool)$nameFound) {
                $name = $name[1];
                $author->setName($name);
            }
            // get author email if exist
            $emailFound = preg_match(self::AUTHOR_EMAIL_PATTERN, $commit, $email);
            if((bool)$emailFound) {
                $email = $email[1];
                $author->setEmail($email);
            }
            $commitObject->setAuthor($author);

            $commitData[] = $commitObject;
        }
        return $commitData;
    }
}