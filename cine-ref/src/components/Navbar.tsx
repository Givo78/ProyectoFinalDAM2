import Link from "next/link";

export default function Navbar() {
  return (
    <nav className="sticky top-0 z-50 bg-neutral-950/80 backdrop-blur border-b border-neutral-800">
      <div className="max-w-7xl mx-auto px-4 h-14 flex items-center justify-between">
        <Link href="/" className="font-bold text-lg">
          CineRef
        </Link>

        <div className="flex gap-4 text-sm text-neutral-400">
          <Link href="/">Explore</Link>
          <Link href="/boards">Boards</Link>
        </div>
      </div>
    </nav>
  );
}
