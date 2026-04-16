# Laravel Boost Integration

This directory contains the official Laravel Boost integration for this package, providing AI agents with guidelines and skills for repository and service generation.

## Structure Overview

```
resources/boost/
├── guidelines/
│   └── core.blade.php              # AI guidance and conventions
└── skills/
    ├── repository-generator/
    │   └── SKILL.md               # Skill: Generate repositories
    ├── service-generator/
    │   └── SKILL.md               # Skill: Generate services
    └── service-binding/
        └── SKILL.md               # Skill: DI and binding patterns
```

## What's in Each Directory

### Guidelines
**Location:** `guidelines/core.blade.php`

Contains upfront AI guidance about:
- Package conventions and structure
- Available commands
- Best practices for code generation
- Repository and service patterns
- Dependency injection approaches
- Testing strategies

This file is loaded automatically by Boost to provide context to AI agents.

### Skills
**Location:** `skills/{skill-name}/SKILL.md`

Three specialized skills for different tasks:

1. **repository-generator** - Helps generate repositories with proper patterns
2. **service-generator** - Helps create services with business logic
3. **service-binding** - Explains dependency injection and container binding

Each skill is an Agent Skills format file with examples and best practices.

## How Users Access This

### Initial Setup

```bash
# 1. User installs Laravel Boost
composer require laravel/boost

# 2. Boost discovers package skills
php artisan boost:install --discover

# 3. User selects desired skills from this package
# (Only shows if package is installed)
```

### Using the Skills

Once installed, users ask their AI agent:

```
"Generate a User repository for managing user data"

# Boost activates repository-generator skill and provides guidance
```

```
"Create an API service for order processing"

# Boost activates service-generator skill with templates
```

```
"I need to understand how to bind custom implementations"

# Boost activates service-binding skill with examples
```

## Guidelines vs Skills

### Guidelines (`core.blade.php`)
- Loaded **upfront** into AI context
- Provides foundational knowledge about the package
- Covers conventions, structure, and best practices
- Always available to the AI agent

### Skills (`skills/*/SKILL.md`)
- Loaded **on-demand** based on conversation
- Focused on specific tasks
- Contain practical examples and patterns
- Suggested by Boost when relevant

## File Format

### Guidelines (Blade Template)
```blade
<x-guide name="Repositories">
  ... guidance ...
</x-guide>
```

These are Blade templates that can reference package variables and are composable with other packages.

### Skills (Agent Skills Standard)
```yaml
---
name: repository-generator
description: Generate repositories with best practices...
---

# Skill content in markdown
```

Skills follow the Agent Skills standard from [agentskills.io](https://agentskills.io) with:
- YAML frontmatter (`name` and `description` required)
- Markdown content with examples
- Clear, actionable guidance

## Development Guidelines

When adding or modifying content in this directory:

### For Guidelines
1. Add guidance that applies broadly to multiple patterns
2. Keep language simple and clear
3. Include specific command examples
4. Reference the overall package design

### For Skills
1. Focus on one specific task
2. Include multiple examples
3. Show common patterns and edge cases
4. Add code examples where helpful

### Before Committing
1. Verify YAML frontmatter in all skills
2. Test that examples are accurate
3. Check for broken references
4. Ensure content is helpful and clear

## References

- **Main Integration Guide:** [BOOST.md](../BOOST.md)
- **Technical Reference:** [BOOST_OFFICIAL_IMPLEMENTATION.md](../BOOST_OFFICIAL_IMPLEMENTATION.md)
- **Verification Checklist:** [BOOST_VERIFICATION_SUMMARY.md](../BOOST_VERIFICATION_SUMMARY.md)
- **Official Docs:** [laravel.com/docs/13.x/boost](https://laravel.com/docs/13.x/boost)

## Support

For more information:
- Read [BOOST.md](../BOOST.md) for integration details
- Check the [README.md](../README.md) for overview
- See individual skill files for specific guidance
- Refer to Laravel Boost official documentation

---

**Status:** ✅ Active - Ready for use with Laravel Boost
