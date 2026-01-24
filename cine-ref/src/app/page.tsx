import Link from "next/link";

type Movie = {
  id: number;
  title: string;
  poster_path: string;
};

async function getMovies(): Promise<Movie[]> {
  const res = await fetch("http://localhost:3000/api/movies", { cache: "no-store" });

  if (!res.ok) {
    throw new Error("Failed to fetch movies");
  }

  const data = await res.json();
  return data.results;
}

export default async function Home() {
  const movies = await getMovies();

  return (
    <main className="max-w-7xl mx-auto px-4 py-6">
      <h1 className="text-2xl font-semibold mb-6">Explorar películas</h1>

      <section className="grid grid-cols-2 md:grid-cols-4 gap-4">
        {movies.map((movie) => (
          <Link key={movie.id} href={`/movie/${movie.id}`} className="group">
            <div className="aspect-[2/3] overflow-hidden rounded-xl bg-neutral-800">
              <img
                src={`https://image.tmdb.org/t/p/w500${movie.poster_path}`}
                alt={movie.title}
                className="w-full h-full object-cover group-hover:scale-105 transition"
              />
            </div>
            <p className="mt-2 text-sm text-center text-neutral-300">{movie.title}</p>
          </Link>
        ))}
      </section>
    </main>
  );
}
