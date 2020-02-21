Introduction
======
[![MIT licensed](https://img.shields.io/badge/license-MIT-blue.svg)](./LICENSE) 
[![Travis Build Status](https://travis-ci.com/famoser/vseth-musikzimmer-pay.svg?branch=master)](https://travis-ci.com/famoser/vseth-musikzimmer-pay)
[![Scrutinizer](https://scrutinizer-ci.com/g/famoser/vseth-musikzimmer-pay/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/famoser/vseth-musikzimmer-pay)

Goals:
 - create a semesterly report quick & informs about VSETH structures.
 - evaluate semesterly reports efficiently & export the information.
 
Testing:
 - request `/login/code/1234` to login as an organisation
 - use `ia@vseth.ethz.ch` `secret` at `/login` to login as an administrator

Release:
 - execute `./vendor/bin/agnes release v1.0 master` to create release `v1.0` from master (ensure the GITHUB_AUTH_TOKEN in `.env` is set)
