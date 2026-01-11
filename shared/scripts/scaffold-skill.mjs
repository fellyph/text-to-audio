import fs from "node:fs";
import path from "node:path";
import process from "node:process";

const [skillName, description] = process.argv.slice(2);

if (!skillName) {
  console.error('Usage: node scaffold-skill.mjs <skill-name> "<description>"');
  process.exit(1);
}

const skillDir = path.join(process.cwd(), "skills", skillName);
const skillFile = path.join(skillDir, "SKILL.md");
const referencesDir = path.join(skillDir, "references");
const scriptsDir = path.join(skillDir, "scripts");
const scenarioFile = path.join(
  process.cwd(),
  "eval",
  "scenarios",
  `${skillName}.md`
);

// Create directories
[skillDir, referencesDir, scriptsDir, path.dirname(scenarioFile)].forEach(
  (dir) => {
    if (!fs.existsSync(dir)) {
      fs.mkdirSync(dir, { recursive: true });
      console.log(`Created directory: ${dir}`);
    }
  }
);

// Create SKILL.md
const skillTemplate = `---
name: ${skillName}
description: "${description || ""}"
compatibility: "Targets WordPress 6.9+ (PHP 7.2.24+). Filesystem-based agent with bash + node. Some workflows require WP-CLI."
---

# ${skillName
  .split("-")
  .map((w) => w.charAt(0).toUpperCase() + w.slice(1))
  .join(" ")}

## When to use

- 

## Inputs required

- 

## Procedure

1. 

## Verification

- 

## Failure modes / debugging

- 

## Escalation

- 
`;

if (!fs.existsSync(skillFile)) {
  fs.writeFileSync(skillFile, skillTemplate);
  console.log(`Created skill: ${skillFile}`);
}

// Create scenario stub
const scenarioTemplate = `# Scenario: ${skillName} basic usage

## Context
A WordPress project.

## Prompt
TBD

## Expected Behavior
The agent should...
`;

if (!fs.existsSync(scenarioFile)) {
  fs.writeFileSync(scenarioFile, scenarioTemplate);
  console.log(`Created scenario: ${scenarioFile}`);
}
