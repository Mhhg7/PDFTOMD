/**
 * Al-Qawsan Scientific Bureau — component types.
 *
 * The bundle is a classic script that assigns `window.AlQawsan`. Load
 * tokens.css, then bundle.css, then React 18 and ReactDOM 18, then bundle.js.
 *
 * Every component takes `lang`. `"ar"` sets `dir="rtl"`, the Arabic type styles
 * (one size step up, looser leading, no tracking) and mirrors the layout through
 * logical properties. It does not translate content — you pass Arabic strings.
 */

import type { ReactNode, MouseEventHandler, ChangeEventHandler } from "react";

export type Lang = "en" | "ar";

export interface BaseProps {
  /** `"ar"` sets dir="rtl" and the Arabic type styles. Default `"en"`. */
  lang?: Lang;
  className?: string;
}

export interface ButtonProps extends BaseProps {
  /** `accent` is the marketing CTA — at most one per section. `danger` is for destructive actions only. */
  variant?: "primary" | "accent" | "secondary" | "ghost" | "danger";
  size?: "sm" | "md" | "lg";
  /** Renders an `<a>` instead of a `<button>`. */
  href?: string;
  type?: "button" | "submit" | "reset";
  disabled?: boolean;
  onClick?: MouseEventHandler;
  /** An icon node at the leading edge. Directional icons are mirrored in RTL. */
  iconStart?: ReactNode;
  iconEnd?: ReactNode;
  children?: ReactNode;
}
export declare function Button(props: ButtonProps): JSX.Element;

export interface SectionHeadingProps extends BaseProps {
  /** Eyebrow label. Uppercased and tracked in English, neither in Arabic. */
  overline?: ReactNode;
  title: ReactNode;
  lead?: ReactNode;
  level?: "h1" | "h2" | "h3" | "h4";
  /** Set on navy or gradient grounds: white title, navy-200 lead, gold overline. */
  onDark?: boolean;
}
export declare function SectionHeading(props: SectionHeadingProps): JSX.Element;

export interface CardProps extends BaseProps {
  /** `dark` is the Depth gradient; `accent` is orange and carries navy copy. */
  variant?: "content" | "dark" | "accent";
  title?: ReactNode;
  overline?: ReactNode;
  body?: ReactNode;
  /** Renders the 48px number circle at the leading edge. */
  number?: ReactNode;
  children?: ReactNode;
}
export declare function Card(props: CardProps): JSX.Element;

export interface StatTileProps extends BaseProps {
  /** The figure itself — "18", "1,500+", "100%". Western digits, tabular. */
  value: ReactNode;
  label: ReactNode;
  /** Tiles alternate across a row. Default `"navy"`. */
  tone?: "navy" | "orange";
  /** Set `false` to drop the hexagon pattern layer. */
  pattern?: boolean;
}
export declare function StatTile(props: StatTileProps): JSX.Element;

export interface BadgeProps extends BaseProps {
  tone?: "brand" | "accent" | "success" | "warning" | "danger" | "info";
  /** The status dot. Keep it — status never travels on colour alone. */
  dot?: boolean;
  children?: ReactNode;
}
export declare function Badge(props: BadgeProps): JSX.Element;

export interface TimelineItem {
  year: ReactNode;
  title?: ReactNode;
  description?: ReactNode;
}
export interface TimelineProps extends BaseProps {
  items: TimelineItem[];
  /** Horizontal on desktop, vertical on mobile. Direction follows `lang`. */
  orientation?: "horizontal" | "vertical";
}
export declare function Timeline(props: TimelineProps): JSX.Element;

export interface FieldProps extends BaseProps {
  label: ReactNode;
  id?: string;
  /** `tel`, `email`, `url` and `number` are forced to dir="ltr" inside RTL forms. */
  type?: string;
  placeholder?: string;
  value?: string;
  defaultValue?: string;
  onChange?: ChangeEventHandler<HTMLInputElement>;
  hint?: ReactNode;
  /** Sets aria-invalid and the danger border, and renders the message. */
  error?: ReactNode;
  required?: boolean;
  disabled?: boolean;
  /** Override the input direction. */
  dir?: "ltr" | "rtl";
}
export declare function Field(props: FieldProps): JSX.Element;

export interface DataTableColumn {
  key: string;
  label: ReactNode;
  /** `"end"` right-aligns in LTR and left-aligns in RTL — use it for every number. */
  align?: "start" | "end";
}
export interface DataTableProps extends BaseProps {
  columns: DataTableColumn[];
  rows: Array<Record<string, ReactNode>>;
  /** Gray header instead of navy, for dense internal tables. */
  dense?: boolean;
  /** Source and period line above the table. */
  caption?: ReactNode;
}
export declare function DataTable(props: DataTableProps): JSX.Element;

export interface NavItem {
  label: ReactNode;
  href?: string;
  active?: boolean;
}
export interface SiteHeaderProps extends BaseProps {
  /** The wordmark slot. Pass the Arc logo image once it is in the Logos group. */
  brand?: ReactNode;
  items?: NavItem[];
  /** Label for the accent CTA at the trailing edge. */
  cta?: ReactNode;
}
export declare function SiteHeader(props: SiteHeaderProps): JSX.Element;

export interface FooterLine {
  text: ReactNode;
  href?: string;
  /** Phone numbers, emails and URLs stay LTR in Arabic footers. */
  ltr?: boolean;
}
export interface SiteFooterProps extends BaseProps {
  columns?: Array<{ title: ReactNode; lines?: FooterLine[] }>;
  legal?: ReactNode;
}
export declare function SiteFooter(props: SiteFooterProps): JSX.Element;

export interface HexPatternProps extends BaseProps {
  /** `field` is the background layer; `cluster` is the focal group lockup — one per layout. */
  variant?: "field" | "cluster";
  /** Picks frame, node and connector colours for the ground it sits on. */
  tone?: "navy" | "orange" | "light" | "watermark";
  width?: number;
  height?: number;
  /** Hexagon circumradius in px. */
  cell?: number;
  /** Overrides the tone's opacity. Never above 0.12 on a text-bearing surface. */
  opacity?: number | string;
  preserveAspectRatio?: string;
}
export declare function HexPattern(props: HexPatternProps): JSX.Element;
