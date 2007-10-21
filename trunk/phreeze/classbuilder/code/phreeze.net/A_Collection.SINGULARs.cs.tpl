using System.Collections;
using System.Text;
using MySql.Data.MySqlClient;
using Com.VerySimple.Phreeze;

namespace Affinity
{ldelim}
	/// <summary>
	/// Used for querying and storing a collection of {$singular} objects
	/// </summary>
	public class {$singular}s : Queryable
	{ldelim}

		/// <summary>
		/// 
		/// </summary>
		/// <param name="phreezer"></param>
		public {$singular}s(Phreezer phreezer)
			: base(phreezer)
		{ldelim}
		{rdelim}

		/// <summary>
		/// returns the type of object that this will store
		/// </summary>
		/// <returns></returns>
		public override System.Type GetObjectType()
		{ldelim}
			return typeof({$singular});
		{rdelim}

		/// <summary>
		/// 
		/// </summary>
		/// <param name="reader"></param>
		public override void Consume(MySqlDataReader reader)
		{ldelim}
			while (reader.Read())
			{ldelim}
				this.Add(new {$singular}(this.phreezer, reader));
			{rdelim}
		{rdelim}
	{rdelim}
{rdelim}