<?php

namespace TNO\ContactForm7\Applications\Contracts;

interface Application {
	function getName(): string;

	function getNamespace(): string;

	function getAppDir(): string;
}