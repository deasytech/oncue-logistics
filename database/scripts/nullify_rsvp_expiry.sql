-- ============================================================
-- RSVP Expiry Cleanup Script
-- ============================================================
-- Purpose:
--   1. Set rsvp_expires_at = NULL for all guests who have NOT
--      yet responded to their RSVP invitation.
--   2. Safe to re-run: only affects rows where
--      rsvp_responded_at IS NULL and rsvp_expires_at IS NOT NULL.
--
-- Run this once in your online database console/tool (e.g.
-- phpMyAdmin, TablePlus, Adminer, MySQL Workbench, etc.).
-- ============================================================

-- Preview how many rows will be affected (run this SELECT first)
SELECT
    COUNT(*) AS rows_to_update
FROM
    event_guest
WHERE
    rsvp_responded_at IS NULL
    AND rsvp_expires_at IS NOT NULL;

-- ============================================================
-- Perform the update
-- ============================================================
UPDATE event_guest
SET
    rsvp_expires_at = NULL
WHERE
    rsvp_responded_at IS NULL
    AND rsvp_expires_at IS NOT NULL;

-- ============================================================
-- Verify: confirm all un-responded RSVPs now have no expiry
-- ============================================================
SELECT
    COUNT(*) AS remaining_with_expiry
FROM
    event_guest
WHERE
    rsvp_responded_at IS NULL
    AND rsvp_expires_at IS NOT NULL;
-- Expected result: 0