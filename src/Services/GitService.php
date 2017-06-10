<?php
namespace TeamNeusta\PushMe\Services;


class GitService
{
    public function getVersionFromGit ()
    {
        exec ('git describe --abbrev=0 --tags', $returnData);

        return reset ($returnData);
    }
}