# TimeRec

_Version 1.0_

TimeRec a web app to record how long you work on your various projects each week. 

## Usage
The first column of the table lists all you projects (or groups of projects).
One project might have many parts you want to count seperately, so they are displayed with an indent under the parent project.
This leads to a tree structure of projects.
You can only record the leaf-projects (the ones with no sub-projects).
The other projects are recorded automatically as soon as a sub-project is recording.

To toggle the time recording of a project, just click its name.
All currently recording projects are displayed in red.


## Deployment
* Clone the repo.
* Change the contents of `conf.json` to your projects.
  You can add, remove and nest as many projects as you like.
* Create an empty file called `active.log`.