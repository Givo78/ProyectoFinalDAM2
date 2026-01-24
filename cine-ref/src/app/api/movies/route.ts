import { NextResponse } from "next/server";

const TMDB_URL = "https://api.themoviedb.org/3";

export async function GET() {
  const res = await fetch(
    `${TMDB_URL}/movie/popular?api_key=${process.env.TMDB_API_KEY}&language=es-ES&page=1`
  );

  if (!res.ok) {
    return NextResponse.json({ error: "Failed to fetch movies" }, { status: 500 });
  }

  const data = await res.json();
  return NextResponse.json(data);
}
