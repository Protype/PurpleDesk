   tea - command line tool to interact with Gitea
   version 0.8.0-preview

 USAGE
   tea command [subcommand] [command options] [arguments...]

 DESCRIPTION
   tea is a productivity helper for Gitea. It can be used to manage most entities on
   one or multiple Gitea instances & provides local helpers like 'tea pr checkout'.
   
   tea tries to make use of context provided by the repository in $PWD if available.
   tea works best in a upstream/fork workflow, when the local main branch tracks the
   upstream repo. tea assumes that local git state is published on the remote before
   doing operations with tea.    Configuration is persisted in $XDG_CONFIG_HOME/tea.

 COMMANDS
   help, h  Shows a list of commands or help for one command
   ENTITIES:
     issues, issue, i                    List, create and update issues
     pulls, pull, pr                     Manage and checkout pull requests
     labels, label                       Manage issue labels
     milestones, milestone, ms           List and create milestones
     releases, release, r                Manage releases
     release assets, release asset, r a  Manage release attachments
     times, time, t                      Operate on tracked times of a repository's issues & pulls
     organizations, organization, org    List, create, delete organizations
     repos, repo                         Show repository details
     comment, c                          Add a comment to an issue / pr
   HELPERS:
     open, o                         Open something of the repository in web browser
     notifications, notification, n  Show notifications
     clone, C                        Clone a repository locally
   SETUP:
     logins, login                  Log in to a Gitea server
     logout                         Log out from a Gitea server
     whoami                         Show current logged in user

 OPTIONS
   --help, -h     show help (default: false)
   --version, -v  print the version (default: false)

 EXAMPLES
   tea login add                       # add a login once to get started

   tea pulls                           # list open pulls for the repo in $PWD
   tea pulls --repo $HOME/foo          # list open pulls for the repo in $HOME/foo
   tea pulls --remote upstream         # list open pulls for the repo pointed at by
                                       # your local "upstream" git remote
   # list open pulls for any gitea repo at the given login instance
   tea pulls --repo gitea/tea --login gitea.com

   tea milestone issues 0.7.0          # view open issues for milestone '0.7.0'
   tea issue 189                       # view contents of issue 189
   tea open 189                        # open web ui for issue 189
   tea open milestones                 # open web ui for milestones

   # send gitea desktop notifications every 5 minutes (bash + libnotify)
   while :; do tea notifications --mine -o simple | xargs -i notify-send {}; sleep 300; done

 ABOUT
   Written & maintained by The Gitea Authors.
   If you find a bug or want to contribute, we'll welcome you at https://gitea.com/gitea/tea.
   More info about Gitea itself on https://about.gitea.com.

---

NAME:
   tea pulls - Manage and checkout pull requests

USAGE:
   tea pulls [command [command options]] [<pull index>]

CATEGORY:
   ENTITIES

DESCRIPTION:
   Lists PRs when called without argument. If PR index is provided, will show it in detail.

COMMANDS:
   list, ls          List pull requests of the repository
   checkout, co      Locally check out the given PR
   clean             Deletes local & remote feature-branches for a closed pull request
   create, c         Create a pull-request
   close             Change state of one or more pull requests to 'closed'
   reopen, open      Change state of one or more pull requests to 'open'
   review            Interactively review a pull request
   approve, lgtm, a  Approve a pull request
   reject            Request changes to a pull request
   merge, m          Merge a pull request

OPTIONS:
   --comments                   Whether to display comments (will prompt if not provided & run interactively) (default: false)
   --fields string, -f string   Comma-separated list of fields to print. Available values:
                                    index,state,author,author-id,url,title,body,mergeable,base,base-commit,head,diff,patch,created,updated,deadline,assignees,milestone,labels,comments
                                   (default: "index,title,state,author,milestone,updated,labels")
   --state string               Filter by state (all|open|closed) (default: open)
   --page string, -p string     specify page, default is 1
   --limit string, --lm string  specify limit of items per page
   --repo string, -r string     Override local repository path or gitea repository slug to interact with. Optional
   --remote string, -R string   Discover Gitea login from remote. Optional
   --login string, -l string    Use a different Gitea Login. Optional
   --output string, -o string   Output format. (simple, table, csv, tsv, yaml, json)
   --help, -h                   show help

---

NAME:
   tea comment - Add a comment to an issue / pr

USAGE:
   tea comment [options] <issue / pr index> [<comment body>]

CATEGORY:
   ENTITIES

DESCRIPTION:
   Add a comment to an issue / pr

OPTIONS:
   --repo string, -r string    Override local repository path or gitea repository slug to interact with. Optional
   --remote string, -R string  Discover Gitea login from remote. Optional
   --login string, -l string   Use a different Gitea Login. Optional
   --output string, -o string  Output format. (simple, table, csv, tsv, yaml, json)
   --help, -h                  show help