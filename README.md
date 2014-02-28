reviewlead
==========

A lightweight code review tool.

When and why choose Reviewlead?
-------------------------------

Most code review tools are patch-oriented. A patch-oriented approach complicates things because -at the end- a patch impacts the code.

- At first, it must be clearly defined how code review fits into the development process: who's responsible for applying the patch, is there any patch approval, what about change management, release management etc. The patch control flow must be well defined.
- Secondly, tool integration is more complex. At server side because of the connection with the version control system. At client side because of the installation of the developer's toolset (except if it's fully web-driven).
- Finally, developers might need some training, not only at tool level (complex tools typically have complex UI's), but also at process level.

This makes patch-oriented tools applicable to companies that have a well-defined development process and are able to spend the effort of introducing and maintaining a rather complex tool.

Reviewlead follows a comment-oriented approach. It's the 'pen and paper' method (i.e. printing the code on paper and then writing comments next to the code with a pen) but then with the latest web technology so that all reviews are stored and that remote teams can review each other's code. How comments result in code changes is not part of the tool. As a consequence the tool is only loosely coupled with the development process, is easily deployed (no integration) and is trivial to learn and use. Therefore it tackles the above 3 drawbacks of patch-oriented tools.

Comment-oriented tools are suitable for companies that prefer easy, straightforward and lightweight code reviewing.

What can Reviewlead do for you?
-------------------------------

If you are a project manager or team leader...

- improve code quality by organizing code reviews
- create a team for each different project
- connect remote teams via a centralized web application
- store and track reviews: no more (lost) paperwork

If you are a developer...

- enjoy an easy and fast workflow: upload source code files and review code by adding comments
- enjoy a team-oriented tool: team members have full control, no supervisor, no time-delaying task assignments, no change approvals
- enjoy an efficient ajax-driver 1-click user interface

If you are a software trainer...

- use Reviewlead as a code presentation tool
- create a team for each course
- put your course material online

Requirements
------------

Server requirements:
- Apache webserver
- Mysql database
- PHP 5.2 or higher

Client requirements:
- Javascript-enabled web browser
