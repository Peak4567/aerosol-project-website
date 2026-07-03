"use client";

import { motion } from "framer-motion";
import type { ReactNode } from "react";

interface RevealProps {
  children: ReactNode;
  direction?: "up" | "left" | "right";
  delay?: number;
  className?: string;
}

const OFFSETS = {
  up: { y: 24, x: 0 },
  left: { y: 0, x: 40 },
  right: { y: 0, x: -40 },
};

export function Reveal({
  children,
  direction = "up",
  delay = 0,
  className,
}: RevealProps) {
  const offset = OFFSETS[direction];
  return (
    <motion.div
      className={className}
      initial={{ opacity: 0, x: offset.x, y: offset.y }}
      whileInView={{ opacity: 1, x: 0, y: 0 }}
      viewport={{ once: true, margin: "-80px" }}
      transition={{ duration: 0.6, delay, ease: "easeOut" }}
    >
      {children}
    </motion.div>
  );
}
