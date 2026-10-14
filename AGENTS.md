<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

## Foundational Context

This application is a Laravel application running on PHP 8.3. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

=== boost rules ===

# Laravel Boost

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Run tests and linting after making changes.

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line blocks.
- Prefer explicit method visibility (`public`, `protected`, `private`) over defaults.
- Always declare types for parameters, return values, and properties where possible. Use union and intersection types when appropriate.
- Prefer constructor property promotion where possible.
- Avoid using `eval()`, `extract()`, or variable variables (`$$var`).
- Keep classes small and focused on a single responsibility.
- Use docblocks only to document complex logic or provide type info where PHP's type system is insufficient. Do not write obvious comments like `// gets the id`.
- Avoid dead code: remove unused `use` statements, variables, and methods.
- Avoid deep nesting: use early returns and guard clauses instead of multiple nested `if`/`else` blocks.
- Do not add comments that simply repeat the method or variable name. Prefer clear code over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

# Pest

- This project uses Pest. Create tests with `php artisan make:test --pest {name}`.
- Run `php artisan test --compact` to verify changes.

</laravel-boost-guidelines>
