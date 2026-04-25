# Admin Order Details

## Purpose

Review one order in detail and update its fulfillment status.

## Routes

```bash
GET  /admin/orders/{publicId}
POST /admin/orders/{publicId}/status
```

## Current Behavior

- The page shows a header summary, item snapshots, fulfillment controls, customer info, and shipping details.
- Item rows are rendered from stored order snapshots rather than live product records.
- The fulfillment panel includes a status form plus a timeline for placed, paid, shipped, and delivered milestones.
- Timestamps are displayed when available and fall back to a “not recorded yet” state.
- A cancelled order shows an explicit warning note in the sidebar.

## Data Shown

- Public order id and lifecycle status
- Customer name and email
- Destination summary
- Item quantity, size, unit price, and line total
- Totals for subtotal, shipping, and grand total
- Shipping address snapshot

## Status Options

- `pending`
- `processing`
- `shipped`
- `delivered`
- `cancelled`

## Visual Reference

Add image here: admin order detail screen with the fulfillment sidebar and item snapshot list.
Suggested path: `docs/screenshots/admin/order-detail.png`
