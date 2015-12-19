This document specifies how to contribute code to this repository.


## Git Workflow

ntb's git workflow encompasses the following key aspects. (For general git style guidelines, see
<https://github.com/agis-/git-style-guide>.)

 * The `master` branch always reflects a *production-ready* state, i.e., the latest release version.
 * The `develop` branch is the main development branch. A maintainer merges into `master` for a new release when it
 reaches a stable point. The `develop` branch reflects the latest state of development and all test should pass.
 * Simple bugfixes consisting of a single commit can be pushed to `develop`.
 * For new features and non-trivial fixes, we use *feature branches* which branch off `develop` with a naming convention
 of `feature/short-description`. After completing work in a feature branch, check the following steps to prepare for a
 merge back into `develop`:
    * Squash your commits into a single one if necessary
    * Create a pull request to `develop` on github
    * Wait for the results of Travis and fix any reported issues
    * Ask a maintainer to review your work after Travis green lights your changes
    * Address the feedback articulated during the review
    * A maintainer will merge the topic branch into `develop` after it passes the code review